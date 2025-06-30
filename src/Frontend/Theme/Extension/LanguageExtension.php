<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme\Extension;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityExtension;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;
use HeyPanel\Core\System\Language\LanguageDefinition;
use HeyPanel\Frontend\Theme\Aggregate\ThemeTranslationDefinition;

class LanguageExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToManyAssociationField('themeTranslations', ThemeTranslationDefinition::class, 'language_id'))->addFlags(new CascadeDelete())
        );
    }

    public function getEntityName(): string
    {
        return LanguageDefinition::ENTITY_NAME;
    }
}
