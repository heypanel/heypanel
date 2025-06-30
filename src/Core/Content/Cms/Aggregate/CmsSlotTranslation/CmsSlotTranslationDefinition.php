<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Cms\Aggregate\CmsSlotTranslation;

use HeyPanel\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotDefinition;
use HeyPanel\Core\Content\Cms\DataAbstractionLayer\Field\SlotConfigField;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;

class CmsSlotTranslationDefinition extends EntityTranslationDefinition
{
    final public const ENTITY_NAME = 'cms_slot_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return CmsSlotTranslationEntity::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function getParentDefinitionClass(): string
    {
        return CmsSlotDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new SlotConfigField('config', 'config'))->addFlags(new ApiAware()),
            (new CustomFields())->addFlags(new ApiAware()),
        ]);
    }
}
