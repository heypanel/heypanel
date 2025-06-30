<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\LandingPage;

use HeyPanel\Core\Content\Cms\CmsPageDefinition;
use HeyPanel\Core\Content\LandingPage\Aggregate\LandingPageChannel\LandingPageChannelDefinition;
use HeyPanel\Core\Content\LandingPage\Aggregate\LandingPageTag\LandingPageTagDefinition;
use HeyPanel\Core\Content\LandingPage\Aggregate\LandingPageTranslation\LandingPageTranslationDefinition;
use HeyPanel\Core\Content\Seo\SeoUrl\SeoUrlDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\BoolField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\FkField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\IdField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\VersionField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;
use HeyPanel\Core\System\Channel\ChannelDefinition;
use HeyPanel\Core\System\Tag\TagDefinition;

class LandingPageDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'landing_page';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return LandingPageCollection::class;
    }

    public function getEntityClass(): string
    {
        return LandingPageEntity::class;
    }

    public function since(): ?string
    {
        return '6.4.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        $collection = new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),
            (new VersionField())->addFlags(new ApiAware()),
            (new BoolField('active', 'active'))->addFlags(new ApiAware()),
            (new TranslatedField('name'))->addFlags(new ApiAware(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            (new TranslatedField('customFields'))->addFlags(new ApiAware()),
            (new TranslatedField('slotConfig'))->addFlags(new ApiAware()),
            (new TranslatedField('metaTitle'))->addFlags(new ApiAware()),
            (new TranslatedField('metaDescription'))->addFlags(new ApiAware()),
            (new TranslatedField('keywords'))->addFlags(new ApiAware()),
            (new TranslatedField('url'))->addFlags(new ApiAware()),
            (new TranslationsAssociationField(LandingPageTranslationDefinition::class, 'landing_page_id'))->addFlags(new ApiAware(), new Required()),
            (new ManyToManyAssociationField('tags', TagDefinition::class, LandingPageTagDefinition::class, 'landing_page_id', 'tag_id'))->addFlags(new CascadeDelete()),
            (new FkField('cms_page_id', 'cmsPageId', CmsPageDefinition::class))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('cmsPage', 'cms_page_id', CmsPageDefinition::class, 'id', false))->addFlags(new ApiAware()),
            (new ManyToManyAssociationField('channels', ChannelDefinition::class, LandingPageChannelDefinition::class, 'landing_page_id', 'channel_id'))->addFlags(new CascadeDelete()),
            (new OneToManyAssociationField('seoUrls', SeoUrlDefinition::class, 'foreign_key'))->addFlags(new ApiAware()),
        ]);

        $collection->add((new ReferenceVersionField(CmsPageDefinition::class))->addFlags(new Required(), new ApiAware()));

        return $collection;
    }
}
