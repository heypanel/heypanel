<?php
declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\FieldSerializer;

use HeyPanel\Core\Framework\DataAbstractionLayer\DataAbstractionLayerException;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Field;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\StorageAware;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\DataStack\KeyValuePair;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\WriteParameterBag;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @internal
 */
class FloatFieldSerializer extends AbstractFieldSerializer
{
    public function encode(Field $field, EntityExistence $existence, KeyValuePair $data, WriteParameterBag $parameters): \Generator
    {
        if (!$field instanceof StorageAware) {
            throw DataAbstractionLayerException::invalidSerializerField(self::class, $field);
        }

        $this->validateIfNeeded($field, $existence, $data, $parameters);

        if ($data->getValue() === null) {
            yield $field->getStorageName() => null;

            return;
        }

        yield $field->getStorageName() => (float) $data->getValue();
    }

    public function decode(Field $field, mixed $value): ?float
    {
        return $value === null ? null : (float) $value;
    }

    protected function getConstraints(Field $field): array
    {
        return [
            new NotBlank(),
            new Type('numeric'),
        ];
    }
}
