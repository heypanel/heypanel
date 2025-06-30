<?php declare(strict_types=1);

namespace HeyPanel\Core\System\NumberRange\Aggregate\NumberRangeTranslation;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\StringField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;
use HeyPanel\Core\System\NumberRange\NumberRangeDefinition;

class NumberRangeTranslationDefinition extends EntityTranslationDefinition
{
    final public const ENTITY_NAME = 'number_range_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return NumberRangeTranslationCollection::class;
    }

    public function getEntityClass(): string
    {
        return NumberRangeTranslationEntity::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function getParentDefinitionClass(): string
    {
        return NumberRangeDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))->addFlags(new Required()),
            new StringField('description', 'description'),
            new CustomFields(),
        ]);
    }
}
