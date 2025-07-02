<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Country;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\BoolField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\IdField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\IntField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\StringField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;
use HeyPanel\Core\System\Country\Aggregate\CountryState\CountryStateDefinition;
use HeyPanel\Core\System\Country\Aggregate\CountryTranslation\CountryTranslationDefinition;

class CountryDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'country';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return CountryCollection::class;
    }

    public function getEntityClass(): string
    {
        return CountryEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),

            (new TranslatedField('name'))->addFlags(new ApiAware(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            (new StringField('iso', 'iso'))->addFlags(new ApiAware(), new SearchRanking(SearchRanking::MIDDLE_SEARCH_RANKING)),
            (new IntField('position', 'position'))->addFlags(new ApiAware()),
            (new BoolField('active', 'active'))->addFlags(new ApiAware()),
            (new OneToManyAssociationField('states', CountryStateDefinition::class, 'country_id', 'id'))
                ->addFlags(new ApiAware(), new CascadeDelete()),

            (new TranslationsAssociationField(CountryTranslationDefinition::class, 'country_id'))
                ->addFlags(new ApiAware(), new Required()),
        ]);
    }
}
