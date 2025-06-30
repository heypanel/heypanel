<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Services;

use GuzzleHttp\Exception\ClientException;
use HeyPanel\Core\Framework\Api\Context\AdminApiSource;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityRepository;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use HeyPanel\Core\Framework\Plugin\PluginCollection;
use HeyPanel\Core\Framework\Plugin\PluginEntity;
use HeyPanel\Core\Framework\Store\Authentication\StoreRequestOptionsProvider;
use HeyPanel\Core\Framework\Store\Event\FirstRunWizardFinishedEvent;
use HeyPanel\Core\Framework\Store\Event\FirstRunWizardStartedEvent;
use HeyPanel\Core\Framework\Store\Exception\StoreLicenseDomainMissingException;
use HeyPanel\Core\Framework\Store\Struct\AccessTokenStruct;
use HeyPanel\Core\Framework\Store\Struct\ExtensionStruct;
use HeyPanel\Core\Framework\Store\Struct\FrwState;
use HeyPanel\Core\Framework\Store\Struct\ShopUserTokenStruct;
use HeyPanel\Core\Framework\Store\Struct\StorePluginStruct;
use HeyPanel\Core\System\SystemConfig\SystemConfigService;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 *
 * @final
 */
class FirstRunWizardService
{
    final public const USER_CONFIG_KEY_FRW_USER_TOKEN = 'core.frw.userToken';
    final public const USER_CONFIG_VALUE_FRW_USER_TOKEN = 'frwUserToken';

    private const TRACKING_EVENT_FRW_STARTED = 'First Run Wizard started';
    private const TRACKING_EVENT_FRW_FINISHED = 'First Run Wizard finished';

    private const FRW_MAX_FAILURES = 3;

    public function __construct(
        private readonly StoreService $storeService,
        private readonly SystemConfigService $configService,
        private readonly bool $frwAutoRun,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly FirstRunWizardClient $frwClient,
        private readonly EntityRepository $userConfigRepository,
        private readonly TrackingEventClient $trackingEventClient
    ) {
    }

    public function startFrw(Context $context): void
    {
        $this->trackingEventClient->fireTrackingEvent(self::TRACKING_EVENT_FRW_STARTED);

        $this->eventDispatcher->dispatch(new FirstRunWizardStartedEvent($this->getFrwState(), $context));
    }

    public function frwLogin(string $heypanelId, string $password, Context $context): void
    {
        $accessTokenResponse = $this->frwClient->frwLogin($heypanelId, $password, $context);
        $accessToken = $this->createAccessTokenStruct($accessTokenResponse, $accessTokenResponse['firstRunWizardUserToken']);

        $this->updateFrwUserToken($context, $accessToken);
    }

    public function upgradeAccessToken(Context $context): void
    {
        $accessTokenResponse = $this->frwClient->upgradeAccessToken($context);
        $accessToken = $this->createAccessTokenStruct($accessTokenResponse, $accessTokenResponse['shopUserToken']);

        $this->storeService->updateStoreToken($context, $accessToken);
        $this->configService->set(StoreRequestOptionsProvider::CONFIG_KEY_STORE_SHOP_SECRET, $accessToken->getShopSecret());
        $this->removeFrwUserToken($context);
    }

    public function finishFrw(bool $failed, Context $context): void
    {
        $currentState = $this->getFrwState();

        if ($failed) {
            $newState = FrwState::failedState(null, $currentState->getFailureCount() + 1);
        } else {
            $this->trackingEventClient->fireTrackingEvent(self::TRACKING_EVENT_FRW_FINISHED);
            $newState = FrwState::completedState();
        }

        $this->setFrwStatus($newState);

        $this->eventDispatcher->dispatch(new FirstRunWizardFinishedEvent($newState, $currentState, $context));
    }

    public function frwShouldRun(): bool
    {
        if (!$this->frwAutoRun) {
            return false;
        }

        $status = $this->getFrwState();
        if ($status->isCompleted()) {
            return false;
        }

        if ($status->isFailed() && $status->getFailureCount() > self::FRW_MAX_FAILURES) {
            return false;
        }

        return true;
    }

    /**
     * @throws StoreLicenseDomainMissingException
     * @throws ClientException
     *
     * @return StorePluginStruct[]
     */
    public function getLanguagePlugins(
        PluginCollection $pluginCollection,
        Context $context,
    ): array {
        $languagePlugins = $this->frwClient->getLanguagePlugins($context);

        return $this->mapExtensionData($languagePlugins, $pluginCollection);
    }

    private function setFrwStatus(FrwState $newState): void
    {
        $currentState = $this->getFrwState();
        $completedAt = null;
        $failedAt = null;
        $failureCount = null;

        if ($newState->isCompleted() && $newState->getCompletedAt()) {
            $completedAt = $newState->getCompletedAt()->format(\DateTimeImmutable::ATOM);
        } elseif ($newState->isFailed() && $newState->getFailedAt()) {
            $failedAt = $newState->getFailedAt()->format(\DateTimeImmutable::ATOM);
            $failureCount = $currentState->getFailureCount() + 1;
        }

        $this->configService->set('core.frw.completedAt', $completedAt);
        $this->configService->set('core.frw.failedAt', $failedAt);
        $this->configService->set('core.frw.failureCount', $failureCount);
    }

    /**
     * @param array<string, mixed> $extensions
     *
     * @return StorePluginStruct[]
     */
    private function mapExtensionData(
        array $extensions,
        PluginCollection $pluginCollection
    ): array {
        /** @var StorePluginStruct[] $mappedExtensions */
        $mappedExtensions = [];
        foreach ($extensions as $extension) {
            if (empty($extension['name']) || empty($extension['localizedInfo']['name'])) {
                continue;
            }

            $mappedExtensions[] = (new StorePluginStruct())->assign([
                'name' => $extension['name'],
                'type' => $extension['type'] ?? 'plugin',
                'label' => $extension['localizedInfo']['name'],
                'shortDescription' => $extension['localizedInfo']['shortDescription'] ?? '',

                'iconPath' => $extension['iconPath'] ?? null,
                'category' => $extension['language'] ?? null,
                'region' => $extension['region'] ?? null,
                'manufacturer' => $extension['producer']['name'] ?? null,
                'position' => $extension['priority'] ?? null,
                'isCategoryLead' => $extension['isCategoryLead'] ?? false,
            ]);
        }

        foreach ($mappedExtensions as $storeExtension) {
            if ($storeExtension->getType() !== ExtensionStruct::EXTENSION_TYPE_PLUGIN) {
                continue;
            }

            /** @var PluginEntity|null $plugin */
            $plugin = $pluginCollection->filterByProperty('name', $storeExtension->getName())->first();
            $storeExtension->assign([
                'active' => $plugin ? $plugin->getActive() : false,
                'installed' => $plugin ? ((bool) $plugin->getInstalledAt()) : false,
            ]);
        }

        return $mappedExtensions;
    }

    private function getFrwState(): FrwState
    {
        $completedAt = $this->configService->getString('core.frw.completedAt');
        if ($completedAt !== '') {
            return FrwState::completedState(new \DateTimeImmutable($completedAt));
        }
        $failedAt = $this->configService->getString('core.frw.failedAt');
        if ($failedAt !== '') {
            $failureCount = $this->configService->getInt('core.frw.failureCount');

            return FrwState::failedState(new \DateTimeImmutable($failedAt), $failureCount);
        }

        return FrwState::openState();
    }

    private function updateFrwUserToken(Context $context, AccessTokenStruct $accessToken): void
    {
        /** @var AdminApiSource $contextSource */
        $contextSource = $context->getSource();
        $userId = $contextSource->getUserId();

        $frwUserToken = $accessToken->getShopUserToken()->getToken();
        $id = $this->getFrwUserTokenConfigId($context);

        $context->scope(Context::SYSTEM_SCOPE, function ($context) use ($userId, $frwUserToken, $id): void {
            $this->userConfigRepository->upsert(
                [
                    [
                        'id' => $id,
                        'userId' => $userId,
                        'key' => self::USER_CONFIG_KEY_FRW_USER_TOKEN,
                        'value' => [self::USER_CONFIG_VALUE_FRW_USER_TOKEN => $frwUserToken,
                        ],
                    ],
                ],
                $context
            );
        });
    }

    private function removeFrwUserToken(Context $context): void
    {
        if (!$context->getSource() instanceof AdminApiSource) {
            return;
        }

        $id = $this->getFrwUserTokenConfigId($context);

        if ($id) {
            $context->scope(Context::SYSTEM_SCOPE, function ($context) use ($id): void {
                $this->userConfigRepository->delete([['id' => $id]], $context);
            });
        }
    }

    private function getFrwUserTokenConfigId(Context $context): ?string
    {
        if (!$context->getSource() instanceof AdminApiSource) {
            return null;
        }

        /** @var AdminApiSource $contextSource */
        $contextSource = $context->getSource();

        $criteria = (new Criteria())->addFilter(
            new EqualsFilter('userId', $contextSource->getUserId()),
            new EqualsFilter('key', self::USER_CONFIG_KEY_FRW_USER_TOKEN)
        );

        return $this->userConfigRepository->searchIds($criteria, $context)->firstId();
    }

    /**
     * @param array{shopSecret?: string} $accessTokenData
     * @param array{token: string, expirationDate: string} $userTokenData
     */
    private function createAccessTokenStruct(array $accessTokenData, array $userTokenData): AccessTokenStruct
    {
        $userToken = new ShopUserTokenStruct(
            $userTokenData['token'],
            new \DateTimeImmutable($userTokenData['expirationDate'])
        );

        return new AccessTokenStruct(
            $userToken,
            $accessTokenData['shopSecret'] ?? null,
        );
    }
}
