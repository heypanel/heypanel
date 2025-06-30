<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Flow\DataAbstractionLayer\Field;

use HeyPanel\Core\Content\Flow\DataAbstractionLayer\FieldSerializer\FlowTemplateConfigFieldSerializer;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\JsonField;

/**
 * @internal
 */
class FlowTemplateConfigField extends JsonField
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
        return FlowTemplateConfigFieldSerializer::class;
    }
}
