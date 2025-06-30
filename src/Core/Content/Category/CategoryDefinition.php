<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Category;

use HeyPanel\Core\Content\Category\Aggregate\CategoryTag\CategoryTagDefinition;
use HeyPanel\Core\Content\Category\Aggregate\CategoryTranslation\CategoryTranslationDefinition;
use HeyPanel\Core\Content\Cms\CmsPageDefinition;
use HeyPanel\Core\Content\Media\MediaDefinition;
use HeyPanel\Core\Content\Post\Aggregate\PostCategory\PostCategoryDefinition;
use HeyPanel\Core\Content\Post\PostDefinition;
use HeyPanel\Core\Content\Seo\MainCategory\MainCategoryDefinition;
use HeyPanel\Core\Content\Seo\SeoUrl\SeoUrlDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\AutoIncrementField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\BoolField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ChildCountField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ChildrenAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\FkField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\ReverseInherited;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Runtime;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\WriteProtected;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\IdField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\IntField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ParentAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ParentFkField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\StringField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TreeLevelField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TreePathField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\VersionField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;
use HeyPanel\Core\System\Channel\ChannelDefinition;
use HeyPanel\Core\System\Tag\TagDefinition;

class CategoryDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'category';

    final public const TYPE_PAGE = 'page';

    final public const TYPE_LINK = 'link';

    final public const TYPE_FOLDER = 'folder';

    final public const LINK_TYPE_EXTERNAL = 'external';

    final public const LINK_TYPE_CATEGORY = 'category';

    final public const LINK_TYPE_PRODUCT = 'question';

    final public const LINK_TYPE_LANDING_PAGE = 'landing_page';

    final public const CONFIG_KEY_DEFAULT_CMS_PAGE_CATEGORY = 'core.cms.default_category_cms_page';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return CategoryCollection::class;
    }

    public function getEntityClass(): string
    {
        return CategoryEntity::class;
    }

    public function getDefaults(): array
    {
        return [
            'type' => self::TYPE_PAGE,
        ];
    }

    public function getHydratorClass(): string
    {
        return CategoryHydrator::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),
            (new VersionField())->addFlags(new ApiAware()),

            (new ParentFkField(self::class))->addFlags(new ApiAware()),
            (new ReferenceVersionField(self::class, 'parent_version_id'))->addFlags(new ApiAware(), new Required()),

            (new FkField('after_category_id', 'afterCategoryId', self::class))->addFlags(new ApiAware()),
            (new ReferenceVersionField(self::class, 'after_category_version_id'))->addFlags(new ApiAware(), new Required()),

            (new FkField('media_id', 'mediaId', MediaDefinition::class))->addFlags(new ApiAware()),

            new AutoIncrementField(),

            (new TranslatedField('breadcrumb'))->addFlags(new ApiAware(), new WriteProtected()),
            (new TreeLevelField('level', 'level'))->addFlags(new ApiAware()),
            (new TreePathField('path', 'path'))->addFlags(new ApiAware()),
            (new ChildCountField())->addFlags(new ApiAware()),

            (new StringField('type', 'type'))->addFlags(new ApiAware(), new Required()),
            (new BoolField('visible', 'visible'))->addFlags(new ApiAware()),
            (new BoolField('active', 'active'))->addFlags(new ApiAware()),

            (new BoolField('cmsPageIdSwitched', 'cmsPageIdSwitched'))->addFlags(new Runtime(), new ApiAware()),
            (new IntField('visibleChildCount', 'visibleChildCount'))->addFlags(new Runtime(), new ApiAware()),

            (new TranslatedField('name'))->addFlags(new ApiAware(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            (new TranslatedField('customFields'))->addFlags(new ApiAware()),
            new TranslatedField('slotConfig'),
            (new TranslatedField('linkType'))->addFlags(new ApiAware()),
            (new TranslatedField('internalLink'))->addFlags(new ApiAware()),
            (new TranslatedField('externalLink'))->addFlags(new ApiAware()),
            (new TranslatedField('linkNewTab'))->addFlags(new ApiAware()),
            (new TranslatedField('description'))->addFlags(new ApiAware()),
            (new TranslatedField('metaTitle'))->addFlags(new ApiAware()),
            (new TranslatedField('metaDescription'))->addFlags(new ApiAware()),
            (new TranslatedField('keywords'))->addFlags(new ApiAware()),

            (new ParentAssociationField(self::class, 'id'))->addFlags(new ApiAware()),
            (new ChildrenAssociationField(self::class))->addFlags(new ApiAware()),

            (new ManyToOneAssociationField('media', 'media_id', MediaDefinition::class, 'id', false))->addFlags(new ApiAware()),
            (new TranslationsAssociationField(CategoryTranslationDefinition::class, 'category_id'))->addFlags(new ApiAware(), new Required()),
            (new ManyToManyAssociationField('questions', PostDefinition::class, PostCategoryDefinition::class, 'category_id', 'post_id'))->addFlags(new CascadeDelete(), new ReverseInherited('categories')),
            (new ManyToManyAssociationField('tags', TagDefinition::class, CategoryTagDefinition::class, 'category_id', 'tag_id'))->addFlags(new ApiAware()),

            (new FkField('cms_page_id', 'cmsPageId', CmsPageDefinition::class))->addFlags(new ApiAware()),
            (new ReferenceVersionField(CmsPageDefinition::class))->addFlags(new Required(), new ApiAware()),
            (new ManyToOneAssociationField('cmsPage', 'cms_page_id', CmsPageDefinition::class, 'id', false))->addFlags(new ApiAware()),

            // Reverse Associations not available in client-api
            new OneToManyAssociationField('navigationChannels', ChannelDefinition::class, 'navigation_category_id'),
            new OneToManyAssociationField('footerChannels', ChannelDefinition::class, 'footer_category_id'),
            new OneToManyAssociationField('serviceChannels', ChannelDefinition::class, 'service_category_id'),
            (new OneToManyAssociationField('mainCategories', MainCategoryDefinition::class, 'category_id'))->addFlags(new CascadeDelete()),
            (new OneToManyAssociationField('seoUrls', SeoUrlDefinition::class, 'foreign_key'))->addFlags(new ApiAware()),

            (new IntField('visible_child_count', 'visibleChildCount'))->addFlags(new Runtime(), new ApiAware()),
        ]);
    }
}
