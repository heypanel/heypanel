<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Cms\Aggregate\CmsPageTranslation;

use HeyPanel\Core\Content\Cms\CmsPageDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\StringField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;

class CmsPageTranslationDefinition extends EntityTranslationDefinition
{
    final public const ENTITY_NAME = 'cms_page_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return CmsPageTranslationEntity::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function getParentDefinitionClass(): string
    {
        return CmsPageDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            new StringField('name', 'name'),
            (new CustomFields())->addFlags(new ApiAware()),
        ]);
    }
}
