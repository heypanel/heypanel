<?php
declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\FieldSerializer;

use HeyPanel\Core\Defaults;
use HeyPanel\Core\Framework\DataAbstractionLayer\DataAbstractionLayerException;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Field;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\StorageAware;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\DataStack\KeyValuePair;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\WriteParameterBag;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @internal
 */
class DateTimeFieldSerializer extends AbstractFieldSerializer
{
    public function encode(
        Field $field,
        EntityExistence $existence,
        KeyValuePair $data,
        WriteParameterBag $parameters
    ): \Generator {
        if (!$field instanceof StorageAware) {
            throw DataAbstractionLayerException::invalidSerializerField(self::class, $field);
        }

        $value = $data->getValue();

        if (\is_string($value)) {
            $value = new \DateTimeImmutable($value);
        }

        if (\is_array($value) && \array_key_exists('date', $value)) {
            $value = new \DateTimeImmutable($value['date']);
        }

        $data->setValue($value);
        $this->validateIfNeeded($field, $existence, $data, $parameters);

        if (!$value instanceof \DateTime && !$value instanceof \DateTimeImmutable) {
            yield $field->getStorageName() => null;

            return;
        }

        $value = $value->setTimezone(new \DateTimeZone('UTC'));

        yield $field->getStorageName() => $value->format(Defaults::STORAGE_DATE_TIME_FORMAT);
    }

    public function decode(Field $field, mixed $value): ?\DateTimeInterface
    {
        return $value === null ? null : new \DateTimeImmutable($value);
    }

    protected function getConstraints(Field $field): array
    {
        return [
            new Type(\DateTimeInterface::class),
            new NotNull(),
        ];
    }
}
