<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Media\Subscriber;

use HeyPanel\Core\Content\Media\Aggregate\MediaFolder\MediaFolderDefinition;
use HeyPanel\Core\Content\Media\MediaDefinition;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\Event\EntitySearchedEvent;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
class MediaVisibilityRestrictionSubscriber implements EventSubscriberInterface
{
    /**
     * @return array<string, string|array{0: string, 1: int}|list<array{0: string, 1?: int}>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EntitySearchedEvent::class => 'securePrivateFolders',
        ];
    }

    public function securePrivateFolders(EntitySearchedEvent $event): void
    {
        if ($event->getContext()->getScope() === Context::SYSTEM_SCOPE) {
            return;
        }

        if ($event->getDefinition()->getEntityName() === MediaFolderDefinition::ENTITY_NAME) {
            $event->getCriteria()->addFilter(
                new MultiFilter('OR', [
                    new EqualsFilter('media_folder.configuration.private', false),
                    new EqualsFilter('media_folder.configuration.private', null),
                ])
            );

            return;
        }

        if ($event->getDefinition()->getEntityName() === MediaDefinition::ENTITY_NAME) {
            $event->getCriteria()->addFilter(
                new MultiFilter('OR', [
                    new EqualsFilter('private', false),
                    new MultiFilter('AND', [
                        new EqualsFilter('private', true),
                        new EqualsFilter('mediaFolder.defaultFolder.entity', 'product_download'),
                    ]),
                ])
            );
        }
    }
}
