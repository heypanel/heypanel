<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme\Extension;

use HeyPanel\Core\Content\Media\MediaDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityExtension;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;
use HeyPanel\Frontend\Theme\Aggregate\ThemeMediaDefinition;
use HeyPanel\Frontend\Theme\ThemeDefinition;

class MediaExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToManyAssociationField('themes', ThemeDefinition::class, 'preview_media_id')
        );

        $collection->add(
            new ManyToManyAssociationField('themeMedia', ThemeDefinition::class, ThemeMediaDefinition::class, 'media_id', 'theme_id')
        );
    }

    public function getEntityName(): string
    {
        return MediaDefinition::ENTITY_NAME;
    }
}
