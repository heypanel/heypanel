<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Cms\DataAbstractionLayer\Field;

use HeyPanel\Core\Content\Cms\DataAbstractionLayer\FieldSerializer\SlotConfigFieldSerializer;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\JsonField;

class SlotConfigField extends JsonField
{
    public function __construct(
        string $storageName,
        string $propertyName
    ) {
        $this->storageName = $storageName;
        parent::__construct($storageName, $propertyName);
    }

    protected function getSerializerClass(): string
    {
        return SlotConfigFieldSerializer::class;
    }
}
