<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Category\Subscriber;

use HeyPanel\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use HeyPanel\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexerRegistry;
use HeyPanel\Core\System\Channel\ChannelDefinition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
readonly class CategoryTreeMovedSubscriber implements EventSubscriberInterface
{
    /**
     * @internal
     */
    public function __construct(
        private EntityIndexerRegistry $indexerRegistry
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EntityWrittenContainerEvent::class => 'detectChannelEntryPoints',
        ];
    }

    public function detectChannelEntryPoints(EntityWrittenContainerEvent $event): void
    {
        $properties = ['navigationCategoryId', 'footerCategoryId', 'serviceCategoryId'];

        $channelIds = $event->getPrimaryKeysWithPropertyChange(ChannelDefinition::ENTITY_NAME, $properties);

        if (empty($channelIds)) {
            return;
        }

        $this->indexerRegistry->sendIndexingMessage(['category.indexer', 'product.indexer']);
    }
}
