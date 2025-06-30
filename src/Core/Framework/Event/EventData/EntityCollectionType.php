<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Event\EventData;

class EntityCollectionType implements EventDataType
{
    final public const TYPE = 'collection';

    public function __construct(private readonly string $definitionClass)
    {
    }

    public function toArray(): array
    {
        return [
            'type' => self::TYPE,
            'entityClass' => $this->definitionClass,
        ];
    }
}
