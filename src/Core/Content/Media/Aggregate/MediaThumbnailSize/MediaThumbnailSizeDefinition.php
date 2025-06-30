<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Media\Aggregate\MediaThumbnailSize;

use HeyPanel\Core\Content\Media\Aggregate\MediaFolderConfiguration\MediaFolderConfigurationDefinition;
use HeyPanel\Core\Content\Media\Aggregate\MediaFolderConfigurationMediaThumbnailSize\MediaFolderConfigurationMediaThumbnailSizeDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\IdField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\IntField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;

class MediaThumbnailSizeDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'media_thumbnail_size';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return MediaThumbnailSizeCollection::class;
    }

    public function getEntityClass(): string
    {
        return MediaThumbnailSizeEntity::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),
            (new IntField('width', 'width', 1))->addFlags(new ApiAware(), new Required()),
            (new IntField('height', 'height', 1))->addFlags(new ApiAware(), new Required()),
            new ManyToManyAssociationField('mediaFolderConfigurations', MediaFolderConfigurationDefinition::class, MediaFolderConfigurationMediaThumbnailSizeDefinition::class, 'media_thumbnail_size_id', 'media_folder_configuration_id'),
            (new CustomFields())->addFlags(new ApiAware()),
        ]);
    }
}
