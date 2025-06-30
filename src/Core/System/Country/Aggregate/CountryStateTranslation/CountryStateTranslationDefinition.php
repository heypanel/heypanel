<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Country\Aggregate\CountryStateTranslation;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\StringField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;
use HeyPanel\Core\System\Country\Aggregate\CountryState\CountryStateDefinition;

class CountryStateTranslationDefinition extends EntityTranslationDefinition
{
    final public const ENTITY_NAME = 'country_state_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return CountryStateTranslationCollection::class;
    }

    public function getEntityClass(): string
    {
        return CountryStateTranslationEntity::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function getParentDefinitionClass(): string
    {
        return CountryStateDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))->addFlags(new ApiAware(), new Required()),
            (new CustomFields())->addFlags(new ApiAware()),
        ]);
    }
}
