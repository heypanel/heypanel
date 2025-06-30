<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Sitemap\ScheduledTask;

use HeyPanel\Core\Content\Sitemap\Event\SitemapChannelCriteriaEvent;
use HeyPanel\Core\Content\Sitemap\Service\SitemapExporterInterface;
use HeyPanel\Core\Defaults;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityRepository;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\NotEqualsFilter;
use HeyPanel\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use HeyPanel\Core\System\Channel\Aggregate\ChannelDomain\ChannelDomainEntity;
use HeyPanel\Core\System\Channel\ChannelEntity;
use HeyPanel\Core\System\SystemConfig\SystemConfigService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
#[AsMessageHandler(handles: SitemapGenerateTask::class)]
final class SitemapGenerateTaskHandler extends ScheduledTaskHandler
{
    /**
     * @internal
     */
    public function __construct(
        EntityRepository $scheduledTaskRepository,
        LoggerInterface $logger,
        private readonly EntityRepository $channelRepository,
        private readonly SystemConfigService $systemConfigService,
        private readonly MessageBusInterface $messageBus,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($scheduledTaskRepository, $logger);
    }

    public function run(): void
    {
        $sitemapRefreshStrategy = $this->systemConfigService->getInt('core.sitemap.sitemapRefreshStrategy');
        if ($sitemapRefreshStrategy !== SitemapExporterInterface::STRATEGY_SCHEDULED_TASK) {
            return;
        }

        $criteria = new Criteria();
        $criteria->addAssociation('domains');
        $criteria->addFilter(new NotEqualsFilter('domains.id', null));

        $criteria->addAssociation('type');
        $criteria->addFilter(new EqualsFilter('type.id', Defaults::CHANNEL_TYPE_FRONTEND));

        $context = Context::createCLIContext();

        $this->eventDispatcher->dispatch(
            new SitemapChannelCriteriaEvent($criteria, $context)
        );

        $channels = $this->channelRepository->search($criteria, $context)->getEntities();

        /** @var ChannelEntity $channel */
        foreach ($channels as $channel) {
            if ($channel->getDomains() === null) {
                continue;
            }

            $languageIds = $channel->getDomains()->map(fn (ChannelDomainEntity $channelDomain) => $channelDomain->getLanguageId());

            $languageIds = array_unique($languageIds);

            foreach ($languageIds as $languageId) {
                $this->messageBus->dispatch(new SitemapMessage($channel->getId(), $languageId, null, null, false));
            }
        }
    }
}
