<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Field;

use HeyPanel\Core\Framework\DataAbstractionLayer\FieldSerializer\CronIntervalFieldSerializer;

class CronIntervalField extends Field implements StorageAware
{
    public function __construct(
        private string $storageName,
        string $propertyName
    ) {
        parent::__construct($propertyName);
    }

    public function getStorageName(): string
    {
        return $this->storageName;
    }

    protected function getSerializerClass(): string
    {
        return CronIntervalFieldSerializer::class;
    }
}
