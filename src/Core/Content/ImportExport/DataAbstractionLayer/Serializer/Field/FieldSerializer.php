<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\DataAbstractionLayer\Serializer\Field;

use HeyPanel\Core\Checkout\Order\Aggregate\OrderDelivery\OrderDeliveryDefinition;
use HeyPanel\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionDefinition;
use HeyPanel\Core\Content\ImportExport\ImportExportException;
use HeyPanel\Core\Content\ImportExport\Struct\Config;
use HeyPanel\Core\Defaults;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\Entity;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\AssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\BoolField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\DateField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Field;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\FkField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Computed;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Inherited;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Runtime;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\WriteProtected;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\IdField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\IntField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\JsonField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use HeyPanel\Core\Framework\Plugin\Exception\DecorationPatternException;
use HeyPanel\Core\Framework\Struct\Collection;
use HeyPanel\Core\Framework\Uuid\Uuid;

class FieldSerializer extends AbstractFieldSerializer
{
    /**
     * {@inheritDoc}
     */
    public function serialize(Config $config, Field $field, $value): iterable
    {
        $key = $field->getPropertyName();

        if ($field instanceof ManyToManyAssociationField && $value !== null) {
            $referenceIdField = $field->getReferenceField();
            $ids = implode('|', array_map(static function ($e) use ($referenceIdField) {
                if ($e instanceof Entity) {
                    return $e->getUniqueIdentifier();
                }
                if (\is_array($e)) {
                    return $e[$referenceIdField];
                }

                return null;
            }, \is_array($value) ? $value : iterator_to_array($value)));

            yield $key => $ids;

            return;
        }

        if ($field instanceof AssociationField) {
            if ($value === null || !\in_array($field->getReferenceClass(), [OrderDeliveryDefinition::class, OrderTransactionDefinition::class], true)) {
                return;
            }

            if ($field instanceof OneToManyAssociationField) {
                if ($value instanceof Collection) {
                    $value = $value->first();
                }

                $definition = $field->getReferenceDefinition();
                $entitySerializer = $this->serializerRegistry->getEntity($definition->getEntityName());

                $result = $entitySerializer->serialize($config, $definition, $value);
                yield $field->getPropertyName() => iterator_to_array($result);
            }

            return;
        }

        if ($field instanceof TranslatedField) {
            return;
        }

        if ($field->getFlag(Computed::class)) {
            return;
        }

        if ($field->getFlag(Inherited::class) && $value === null) {
            yield $key => null;

            return;
        }

        if ($field instanceof DateField || $field instanceof DateTimeField) {
            if ($value instanceof \DateTimeInterface) {
                $value = $value->format(Defaults::STORAGE_DATE_TIME_FORMAT);
            }

            if (empty($value)) {
                return;
            }

            yield $key => (string) $value;
        } elseif ($field instanceof BoolField) {
            yield $key => $value === true ? '1' : '0';
        } elseif ($field instanceof JsonField) {
            yield $key => $value === null ? null : json_encode($value, \JSON_THROW_ON_ERROR);
        } else {
            if ($value instanceof \JsonSerializable) {
                $value = $value->jsonSerialize();
            }

            if (\is_array($value)) {
                $value = json_encode($value, \JSON_THROW_ON_ERROR);
            }

            if (!\is_scalar($value) && !$value instanceof \Stringable) {
                yield $key => null;
            }

            $value = $value === null ? $value : (string) $value;
            yield $key => $value;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deserialize(Config $config, Field $field, $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if ($field->is(Computed::class) || $field->is(Runtime::class)) {
            return null;
        }

        $writeProtection = $field->getFlag(WriteProtected::class);
        if ($writeProtection && !$writeProtection->isAllowed(Context::SYSTEM_SCOPE)) {
            return null;
        }

        if ($field instanceof ManyToManyAssociationField) {
            return array_filter(
                array_map(
                    function ($id) {
                        $id = $this->normalizeId($id);
                        if ($id === '') {
                            return null;
                        }

                        return ['id' => $id];
                    },
                    explode('|', (string) $value)
                )
            );
        }

        if ($field instanceof OneToManyAssociationField) {
            // early return in case a specific serializer has already hydrated associations
            if (\is_array($value)) {
                return null;
            }

            return array_filter(
                array_map(
                    function ($id) {
                        $id = $this->normalizeId($id);
                        if ($id === '') {
                            return null;
                        }

                        return $id;
                    },
                    explode('|', (string) $value)
                )
            );
        }

        if ($field instanceof AssociationField) {
            return null;
        }

        if ($field instanceof TranslatedField) {
            return null;
        }

        if (\is_string($value) && trim($value) === '') {
            return null;
        }

        if ($field instanceof DateField || $field instanceof DateTimeField) {
            try {
                return new \DateTimeImmutable((string) $value);
            } catch (\Throwable $previous) {
                throw ImportExportException::deserializationFailed($field->getPropertyName(), $value, 'date');
            }
        }

        if ($field instanceof BoolField) {
            return ScalarTypeSerializer::deserializeBool($config, $field, (string) $value);
        }

        if ($field instanceof JsonField) {
            try {
                return json_decode((string) $value, true, 512, \JSON_THROW_ON_ERROR);
            } catch (\Throwable $previous) {
                throw ImportExportException::deserializationFailed($field->getPropertyName(), $value, 'json');
            }
        }

        if ($field instanceof IntField) {
            return ScalarTypeSerializer::deserializeInt($config, $field, $value);
        }

        if ($field instanceof IdField || $field instanceof FkField) {
            try {
                return $this->normalizeId((string) $value);
            } catch (\Throwable $previous) {
                throw ImportExportException::deserializationFailed($field->getPropertyName(), $value, 'uuid');
            }
        }

        return $value;
    }

    public function supports(Field $field): bool
    {
        return true;
    }

    public function getDecorated(): AbstractFieldSerializer
    {
        throw new DecorationPatternException(self::class);
    }

    private function normalizeId(?string $id): string
    {
        $id = mb_strtolower(trim((string) $id));

        if ($id === '' || Uuid::isValid($id)) {
            return $id;
        }

        if (str_contains($id, '|')) {
            throw ImportExportException::invalidIdentifier($id);
        }

        return Uuid::fromStringToHex($id);
    }
}
