<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\FieldSerializer;

use HeyPanel\Core\Framework\DataAbstractionLayer\DataAbstractionLayerException;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Field;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\DataStack\KeyValuePair;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\WriteParameterBag;

/**
 * @internal
 */
class UpdatedAtFieldSerializer extends DateTimeFieldSerializer
{
    /**
     * @throws DataAbstractionLayerException
     */
    public function encode(
        Field $field,
        EntityExistence $existence,
        KeyValuePair $data,
        WriteParameterBag $parameters
    ): \Generator {
        if (!$field instanceof UpdatedAtField) {
            throw DataAbstractionLayerException::invalidSerializerField(UpdatedAtField::class, $field);
        }
        if (!$existence->exists()) {
            return;
        }

        $data->setValue(new \DateTime());

        yield from parent::encode($field, $existence, $data, $parameters);
    }
}
