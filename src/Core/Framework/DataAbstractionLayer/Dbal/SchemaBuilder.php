<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Dbal;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use HeyPanel\Core\Content\Cms\DataAbstractionLayer\Field\SlotConfigField;
use HeyPanel\Core\Content\Flow\DataAbstractionLayer\Field\FlowTemplateConfigField;
use HeyPanel\Core\Content\Product\DataAbstractionLayer\CheapestPrice\CheapestPriceField;
use HeyPanel\Core\Framework\DataAbstractionLayer\DataAbstractionLayerException;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\AssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\AutoIncrementField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\BlobField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\BoolField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\BreadcrumbField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\CalculatedPriceField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\CartPriceField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\CashRoundingConfigField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ChildCountField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ChildrenAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ConfigJsonField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\CreatedByField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\CronIntervalField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\DateField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\DateIntervalField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\EmailField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\EnumField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Field;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\FkField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\RestrictDelete;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Runtime;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\FloatField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\IdField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\IntField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\JsonField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ListField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\LockedField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ManyToManyIdField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ObjectField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ParentAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ParentFkField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\PasswordField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\PriceDefinitionField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\PriceField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\RemoteAddressField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\SerializedField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\StateMachineStateField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\StorageAware;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\StringField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TaxFreeConfigField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TimeZoneField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TreeBreadcrumbField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TreeLevelField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TreePathField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\UpdatedByField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\VariantListingConfigField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\VersionDataPayloadField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\VersionField;
use HeyPanel\Core\System\NumberRange\DataAbstractionLayer\NumberRangeField;

/**
 * @internal
 */
class SchemaBuilder
{
    /**
     * @var array<string, string>
     */
    public static array $fieldMapping = [
        IdField::class => Types::BINARY,
        FkField::class => Types::BINARY,
        ParentFkField::class => Types::BINARY,
        VersionField::class => Types::BINARY,
        ReferenceVersionField::class => Types::BINARY,
        CreatedByField::class => Types::BINARY,
        UpdatedByField::class => Types::BINARY,
        StateMachineStateField::class => Types::BINARY,

        CreatedAtField::class => Types::DATETIME_MUTABLE,
        UpdatedAtField::class => Types::DATETIME_MUTABLE,
        DateTimeField::class => Types::DATETIME_MUTABLE,

        DateField::class => Types::DATE_MUTABLE,
        SerializedField::class => Types::JSON,
        CartPriceField::class => Types::JSON,
        CalculatedPriceField::class => Types::JSON,
        PriceField::class => Types::JSON,
        PriceDefinitionField::class => Types::JSON,
        JsonField::class => Types::JSON,
        ListField::class => Types::JSON,
        ConfigJsonField::class => Types::JSON,
        CustomFields::class => Types::JSON,
        BreadcrumbField::class => Types::JSON,
        CashRoundingConfigField::class => Types::JSON,
        ObjectField::class => Types::JSON,
        TaxFreeConfigField::class => Types::JSON,
        TreeBreadcrumbField::class => Types::JSON,
        VariantListingConfigField::class => Types::JSON,
        VersionDataPayloadField::class => Types::JSON,
        ManyToManyIdField::class => Types::JSON,
        SlotConfigField::class => Types::JSON,
        FlowTemplateConfigField::class => Types::JSON,
        CheapestPriceField::class => Types::JSON,

        ChildCountField::class => Types::INTEGER,
        IntField::class => Types::INTEGER,
        AutoIncrementField::class => Types::INTEGER,
        TreeLevelField::class => Types::INTEGER,

        BoolField::class => Types::BOOLEAN,
        LockedField::class => Types::BOOLEAN,

        PasswordField::class => Types::STRING,
        StringField::class => Types::STRING,
        TimeZoneField::class => Types::STRING,
        CronIntervalField::class => Types::STRING,
        DateIntervalField::class => Types::STRING,
        EmailField::class => Types::STRING,
        RemoteAddressField::class => Types::STRING,
        NumberRangeField::class => Types::STRING,

        BlobField::class => Types::BLOB,

        FloatField::class => Types::DECIMAL,

        TreePathField::class => Types::TEXT,
        LongTextField::class => Types::TEXT,
    ];

    /**
     * @var array<string, array<string, mixed>>
     */
    public static array $options = [
        Types::BINARY => [
            'length' => 16,
            'fixed' => true,
        ],

        Types::BOOLEAN => [
            'default' => 0,
        ],
    ];

    public function buildSchemaOfDefinition(EntityDefinition $definition): Table
    {
        $table = (new Schema())->createTable($definition->getEntityName());
        $table->addOption('charset', 'utf8mb4');
        $table->addOption('collate', 'utf8mb4_unicode_ci');

        /** @var Field $field */
        foreach ($definition->getFields() as $field) {
            if ($field->is(Runtime::class)) {
                continue;
            }

            if ($field instanceof AssociationField) {
                continue;
            }

            if (!$field instanceof StorageAware) {
                continue;
            }

            if ($field instanceof TranslatedField) {
                continue;
            }

            $fieldType = $this->getFieldType($field);

            $table->addColumn(
                $field->getStorageName(),
                $fieldType,
                $this->getFieldOptions($field, $fieldType, $definition)
            );
        }

        /** @var StorageAware[] $primaryKeys */
        $primaryKeys = $definition->getPrimaryKeys()->filter(function (Field $field) {
            return $field instanceof StorageAware;
        })->getElements();

        $table->setPrimaryKey(array_map(function (StorageAware $field) {
            return $field->getStorageName();
        }, $primaryKeys));

        $this->addForeignKeys($table, $definition);

        return $table;
    }

    private function getFieldType(Field $field): string
    {
        if ($field instanceof EnumField) {
            return $field->getType();
        }

        foreach (self::$fieldMapping as $class => $type) {
            if ($field instanceof $class) {
                return self::$fieldMapping[$field::class];
            }
        }

        throw DataAbstractionLayerException::fieldHasNoType($field->getPropertyName());
    }

    /**
     * @return array<string, mixed>
     */
    private function getFieldOptions(Field $field, string $type, EntityDefinition $definition): array
    {
        $options = self::$options[$type] ?? [];

        $options['notnull'] = false;

        if ($field->is(Required::class) && !$field instanceof UpdatedAtField && !$field instanceof ReferenceVersionField) {
            $options['notnull'] = true;
        }

        if (\array_key_exists($field->getPropertyName(), $definition->getDefaults())) {
            $options['default'] = $definition->getDefaults()[$field->getPropertyName()];
        }

        if ($field instanceof StringField) {
            $options['length'] = $field->getMaxLength();
        }

        if ($field instanceof AutoIncrementField) {
            $options['autoincrement'] = true;
            $options['notnull'] = true;
        }

        if ($field instanceof FloatField) {
            $options['precision'] = 10;
            $options['scale'] = 2;
        }

        return $options;
    }

    private function addForeignKeys(Table $table, EntityDefinition $definition): void
    {
        $fields = $definition->getFields()->filter(
            function (Field $field) {
                if ($field instanceof ManyToOneAssociationField
                    || ($field instanceof OneToOneAssociationField && $field->getStorageName() !== 'id')) {
                    return true;
                }

                return false;
            }
        );

        $referenceVersionFields = $definition->getFields()->filterInstance(ReferenceVersionField::class);

        /** @var ManyToOneAssociationField $field */
        foreach ($fields as $field) {
            $reference = $field->getReferenceDefinition();

            $hasOneToMany = $definition->getFields()->filter(function (Field $field) use ($reference) {
                if (!$field instanceof OneToManyAssociationField) {
                    return false;
                }
                if ($field instanceof ChildrenAssociationField) {
                    return false;
                }

                return $field->getReferenceDefinition() === $reference;
            })->count() > 0;

            // skip foreign key to prevent bi-directional foreign key
            if ($hasOneToMany) {
                continue;
            }

            $columns = [
                $field->getStorageName(),
            ];

            $referenceColumns = [
                $field->getReferenceField(),
            ];

            if ($reference->isVersionAware()) {
                $versionField = null;

                /** @var ReferenceVersionField $referenceVersionField */
                foreach ($referenceVersionFields as $referenceVersionField) {
                    if ($referenceVersionField->getVersionReferenceDefinition() === $reference) {
                        $versionField = $referenceVersionField;

                        break;
                    }
                }

                if ($field instanceof ParentAssociationField) {
                    $columns[] = 'version_id';
                } else {
                    if ($versionField === null) {
                        throw DataAbstractionLayerException::versionFieldNotFound($field->getPropertyName());
                    }

                    /** @var ReferenceVersionField $versionField */
                    $columns[] = $versionField->getStorageName();
                }

                $referenceColumns[] = 'version_id';
            }

            $update = 'CASCADE';

            if ($field->is(CascadeDelete::class)) {
                $delete = 'CASCADE';
            } elseif ($field->is(RestrictDelete::class)) {
                $delete = 'RESTRICT';
            } else {
                $delete = 'SET NULL';
            }

            $table->addForeignKeyConstraint(
                $reference->getEntityName(),
                $columns,
                $referenceColumns,
                [
                    'onUpdate' => $update,
                    'onDelete' => $delete,
                ],
                \sprintf('fk.%s.%s', $definition->getEntityName(), $field->getStorageName())
            );
        }
    }
}
