<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Media\Subscriber;

use HeyPanel\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailCollection;
use HeyPanel\Core\Content\Media\MediaEntity;
use HeyPanel\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;

class MediaLoadedSubscriber
{
    /**
     * @param EntityLoadedEvent<MediaEntity> $event
     */
    public function unserialize(EntityLoadedEvent $event): void
    {
        foreach ($event->getEntities() as $media) {
            if ($media->getMediaTypeRaw()) {
                $media->setMediaType(unserialize($media->getMediaTypeRaw()));
            }

            if ($media->getThumbnails() !== null) {
                continue;
            }

            $thumbnails = match (true) {
                $media->getThumbnailsRo() !== null => unserialize($media->getThumbnailsRo()),
                default => new MediaThumbnailCollection(),
            };

            $media->setThumbnails($thumbnails);
        }
    }
}
