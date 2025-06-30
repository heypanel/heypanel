<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme\Message;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityRepository;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\Notification\NotificationService;
use HeyPanel\Core\Framework\Uuid\Uuid;
use HeyPanel\Core\System\Channel\ChannelCollection;
use HeyPanel\Frontend\Theme\ConfigLoader\AbstractConfigLoader;
use HeyPanel\Frontend\Theme\Exception\ThemeException;
use HeyPanel\Frontend\Theme\FrontendPluginRegistry;
use HeyPanel\Frontend\Theme\ThemeCompilerInterface;
use HeyPanel\Frontend\Theme\ThemeRuntimeConfigService;
use HeyPanel\Frontend\Theme\ThemeService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @internal
 */
#[AsMessageHandler]
final class CompileThemeHandler
{
    /**
     * @param EntityRepository<ChannelCollection> $saleschannelRepository
     */
    public function __construct(
        private readonly ThemeCompilerInterface $themeCompiler,
        private readonly AbstractConfigLoader $configLoader,
        private readonly FrontendPluginRegistry $extensionRegistry,
        private readonly NotificationService $notificationService,
        private readonly EntityRepository $saleschannelRepository,
        private readonly ThemeRuntimeConfigService $runtimeConfigService,
    ) {
    }

    public function __invoke(CompileThemeMessage $message): void
    {
        $message->getContext()->addState(ThemeService::STATE_NO_QUEUE);
        $themeConfig = $this->configLoader->load($message->getThemeId(), $message->getContext());
        $this->themeCompiler->compileTheme(
            $message->getChannelId(),
            $message->getThemeId(),
            $themeConfig,
            $this->extensionRegistry->getConfigurations(),
            $message->isWithAssets(),
            $message->getContext()
        );

        $this->runtimeConfigService->refreshRuntimeConfig(
            $message->getThemeId(),
            $themeConfig,
            $message->getContext(),
            false,
            $this->extensionRegistry->getConfigurations(),
        );

        if ($message->getContext()->getScope() !== Context::USER_SCOPE) {
            return;
        }

        $channel = $this->saleschannelRepository->search(
            new Criteria([$message->getChannelId()]),
            $message->getContext()
        )->getEntities()->first();
        if (!$channel) {
            throw ThemeException::channelNotFound($message->getChannelId());
        }

        $this->notificationService->createNotification(
            [
                'id' => Uuid::randomHex(),
                'status' => 'info',
                'message' => 'Compilation for channel ' . $channel->getName() . ' completed',
                'requiredPrivileges' => [],
            ],
            $message->getContext()
        );
    }
}
