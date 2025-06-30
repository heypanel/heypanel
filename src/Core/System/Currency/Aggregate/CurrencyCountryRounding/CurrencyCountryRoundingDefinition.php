<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Currency\Aggregate\CurrencyCountryRounding;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\CashRoundingConfigField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\FkField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\IdField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;
use HeyPanel\Core\System\Country\CountryDefinition;
use HeyPanel\Core\System\Currency\CurrencyDefinition;

class CurrencyCountryRoundingDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'currency_country_rounding';

    public function since(): ?string
    {
        return '6.4.0.0';
    }

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return CurrencyCountryRoundingCollection::class;
    }

    public function getEntityClass(): string
    {
        return CurrencyCountryRoundingEntity::class;
    }

    protected function getParentDefinitionClass(): ?string
    {
        return CurrencyDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        $fields = new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),

            (new FkField('currency_id', 'currencyId', CurrencyDefinition::class))
                ->addFlags(new Required()),

            (new FkField('country_id', 'countryId', CountryDefinition::class))
                ->addFlags(new Required()),

            (new CashRoundingConfigField('item_rounding', 'itemRounding'))
                ->addFlags(new Required()),

            (new CashRoundingConfigField('total_rounding', 'totalRounding'))
                ->addFlags(new Required()),
        ]);

        // disable dal validation command
        $fields->add(new ManyToOneAssociationField('currency', 'currency_id', CurrencyDefinition::class));
        $fields->add(
            (new ManyToOneAssociationField('country', 'country_id', CountryDefinition::class))
                ->addFlags(new SearchRanking(SearchRanking::ASSOCIATION_SEARCH_RANKING))
        );

        return $fields;
    }
}
