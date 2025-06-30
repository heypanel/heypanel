<?php
declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\FieldSerializer;

use HeyPanel\Core\Framework\DataAbstractionLayer\DataAbstractionLayerException;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Field;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\StringField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TimeZoneField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\DataStack\KeyValuePair;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\WriteParameterBag;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Timezone;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @internal
 */
class TimeZoneFieldSerializer extends AbstractFieldSerializer
{
    public function encode(
        Field $field,
        EntityExistence $existence,
        KeyValuePair $data,
        WriteParameterBag $parameters
    ): \Generator {
        if (!$field instanceof TimeZoneField) {
            throw DataAbstractionLayerException::invalidSerializerField(TimeZoneField::class, $field);
        }

        $this->validateIfNeeded($field, $existence, $data, $parameters);

        yield $field->getStorageName() => $data->getValue() !== null ? (string) $data->getValue() : null;
    }

    public function decode(Field $field, mixed $value): ?string
    {
        if ($value === null) {
            return $value;
        }

        return (string) $value;
    }

    /**
     * @param StringField $field
     *
     * @return Constraint[]
     */
    protected function getConstraints(Field $field): array
    {
        return [
            new Type('string'),
            new Timezone(),
            new NotBlank(),
        ];
    }
}
