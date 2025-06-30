<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Cms\Aggregate\CmsSlot;

use HeyPanel\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockDefinition;
use HeyPanel\Core\Content\Cms\Aggregate\CmsSlotTranslation\CmsSlotTranslationDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\FkField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Runtime;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\WriteProtected;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\IdField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\JsonField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\LockedField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\StringField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\VersionField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;

class CmsSlotDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'cms_slot';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return CmsSlotEntity::class;
    }

    public function getCollectionClass(): string
    {
        return CmsSlotCollection::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function getParentDefinitionClass(): ?string
    {
        return CmsBlockDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),
            (new VersionField())->addFlags(new ApiAware()),
            (new ReferenceVersionField(CmsBlockDefinition::class))->addFlags(new Required(), new ApiAware()),
            (new JsonField('fieldConfig', 'fieldConfig'))->addFlags(new Runtime(), new ApiAware()),

            (new StringField('type', 'type'))->addFlags(new ApiAware(), new Required()),
            (new StringField('slot', 'slot'))->addFlags(new ApiAware(), new Required()),
            (new LockedField())->addFlags(new ApiAware()),
            (new TranslatedField('config'))->addFlags(new ApiAware()),
            (new TranslatedField('customFields'))->addFlags(new ApiAware()),

            (new JsonField('data', 'data'))->addFlags(new ApiAware(), new Runtime(), new WriteProtected()),

            (new FkField('cms_block_id', 'blockId', CmsBlockDefinition::class))->addFlags(new ApiAware(), new Required()),
            (new ManyToOneAssociationField('block', 'cms_block_id', CmsBlockDefinition::class, 'id', false))->addFlags(new ApiAware()),
            (new TranslationsAssociationField(CmsSlotTranslationDefinition::class, 'cms_slot_id'))->addFlags(new ApiAware()),
        ]);
    }
}
