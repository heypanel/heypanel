<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Authentication;

use HeyPanel\Core\Framework\Api\Context\AdminApiSource;
use HeyPanel\Core\Framework\Api\Context\Exception\InvalidContextSourceException;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityRepository;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use HeyPanel\Core\Framework\Store\Services\FirstRunWizardService;
use HeyPanel\Core\System\User\Aggregate\UserConfig\UserConfigEntity;

/**
 * @internal
 */
class FrwRequestOptionsProvider extends AbstractStoreRequestOptionsProvider
{
    private const OINPANEL_TOKEN_HEADER = 'X-HeyPanel-Token';

    public function __construct(
        private readonly AbstractStoreRequestOptionsProvider $optionsProvider,
        private readonly EntityRepository $userConfigRepository,
    ) {
    }

    public function getAuthenticationHeader(Context $context): array
    {
        return array_filter([self::OINPANEL_TOKEN_HEADER => $this->getFrwUserToken($context)]);
    }

    public function getDefaultQueryParameters(Context $context): array
    {
        return $this->optionsProvider->getDefaultQueryParameters($context);
    }

    private function getFrwUserToken(Context $context): ?string
    {
        if (!$context->getSource() instanceof AdminApiSource) {
            throw new InvalidContextSourceException(AdminApiSource::class, $context->getSource()::class);
        }

        /** @var AdminApiSource $contextSource */
        $contextSource = $context->getSource();

        $criteria = (new Criteria())->addFilter(
            new EqualsFilter('userId', $contextSource->getUserId()),
            new EqualsFilter('key', FirstRunWizardService::USER_CONFIG_KEY_FRW_USER_TOKEN),
        );

        /** @var UserConfigEntity|null $userConfig */
        $userConfig = $this->userConfigRepository->search($criteria, $context)->first();

        return $userConfig === null ? null : $userConfig->getValue()[FirstRunWizardService::USER_CONFIG_VALUE_FRW_USER_TOKEN] ?? null;
    }
}
