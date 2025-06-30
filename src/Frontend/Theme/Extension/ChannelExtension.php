<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme\Extension;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityExtension;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;
use HeyPanel\Core\System\Channel\ChannelDefinition;
use HeyPanel\Frontend\Theme\Aggregate\ThemeChannelDefinition;
use HeyPanel\Frontend\Theme\ThemeDefinition;

class ChannelExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new ManyToManyAssociationField('themes', ThemeDefinition::class, ThemeChannelDefinition::class, 'channel_id', 'theme_id')
        );
    }

    public function getEntityName(): string
    {
        return ChannelDefinition::ENTITY_NAME;
    }
}
