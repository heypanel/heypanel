<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\FieldSerializer;

use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ConfigJsonField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Field;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\DataStack\KeyValuePair;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\WriteParameterBag;

/**
 * @internal
 */
class ConfigJsonFieldSerializer extends JsonFieldSerializer
{
    public function encode(Field $field, EntityExistence $existence, KeyValuePair $data, WriteParameterBag $parameters): \Generator
    {
        $wrapped = [ConfigJsonField::STORAGE_KEY => $data->getValue()];
        $data->setValue($wrapped);

        return parent::encode($field, $existence, $data, $parameters);
    }

    public function decode(Field $field, mixed $value): mixed
    {
        $wrapped = parent::decode($field, $value);
        if ($wrapped === null || !isset($wrapped[ConfigJsonField::STORAGE_KEY])) {
            return null;
        }

        return $wrapped[ConfigJsonField::STORAGE_KEY];
    }
}
