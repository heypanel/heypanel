<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Cms;

use HeyPanel\Core\Content\Category\CategoryDefinition;
use HeyPanel\Core\Content\Cms\Aggregate\CmsPageTranslation\CmsPageTranslationDefinition;
use HeyPanel\Core\Content\Cms\Aggregate\CmsSection\CmsSectionDefinition;
use HeyPanel\Core\Content\LandingPage\LandingPageDefinition;
use HeyPanel\Core\Content\Media\MediaDefinition;
use HeyPanel\Core\Content\Post\PostDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\FkField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\RestrictDelete;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\IdField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\JsonField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\LockedField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\StringField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\VersionField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;
use HeyPanel\Core\System\Channel\ChannelDefinition;

class CmsPageDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'cms_page';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return CmsPageEntity::class;
    }

    public function getCollectionClass(): string
    {
        return CmsPageCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),
            (new VersionField())->addFlags(new ApiAware()),
            (new TranslatedField('name'))->addFlags(new ApiAware()),
            (new StringField('type', 'type'))->addFlags(new ApiAware(), new Required()),
            (new StringField('entity', 'entity'))->addFlags(new ApiAware()),
            (new StringField('css_class', 'cssClass'))->addFlags(new ApiAware()),
            (new JsonField('config', 'config', [
                (new StringField('background_color', 'backgroundColor'))->addFlags(new ApiAware()),
            ]))->addFlags(new ApiAware()),
            (new FkField('preview_media_id', 'previewMediaId', MediaDefinition::class))->addFlags(new ApiAware()),
            (new TranslatedField('customFields'))->addFlags(new ApiAware()),
            new LockedField(),

            (new OneToManyAssociationField('sections', CmsSectionDefinition::class, 'cms_page_id'))->addFlags(new ApiAware(), new CascadeDelete()),
            (new TranslationsAssociationField(CmsPageTranslationDefinition::class, 'cms_page_id'))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('previewMedia', 'preview_media_id', MediaDefinition::class, 'id', false))->addFlags(new ApiAware()),

            (new OneToManyAssociationField('categories', CategoryDefinition::class, 'cms_page_id'))->addFlags(new RestrictDelete()),
            (new OneToManyAssociationField('landingPages', LandingPageDefinition::class, 'cms_page_id'))->addFlags(new ApiAware(), new RestrictDelete()),
            (new OneToManyAssociationField('homeChannels', ChannelDefinition::class, 'home_cms_page_id'))->addFlags(new RestrictDelete()),
            (new OneToManyAssociationField('questions', PostDefinition::class, 'cms_page_id'))->addFlags(new RestrictDelete()),
        ]);
    }
}
