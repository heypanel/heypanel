<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\DataAbstractionLayer\Serializer\Entity;

use HeyPanel\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;

abstract class AbstractMediaSerializer extends EntitySerializer
{
    abstract public function persistMedia(EntityWrittenEvent $event): void;
}
