<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Aggregate\ChannelTranslation;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\BoolField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\JsonField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\StringField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;
use HeyPanel\Core\System\Channel\ChannelDefinition;

class ChannelTranslationDefinition extends EntityTranslationDefinition
{
    final public const ENTITY_NAME = 'channel_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return ChannelTranslationCollection::class;
    }

    public function getDefaults(): array
    {
        return [
            'homeEnabled' => true,
        ];
    }

    public function getEntityClass(): string
    {
        return ChannelTranslationEntity::class;
    }

    protected function getParentDefinitionClass(): string
    {
        return ChannelDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))->addFlags(new ApiAware(), new Required()),
            new JsonField('home_slot_config', 'homeSlotConfig'),
            (new BoolField('home_enabled', 'homeEnabled'))->addFlags(new Required()),
            new StringField('home_name', 'homeName'),
            new StringField('home_meta_title', 'homeMetaTitle'),
            new StringField('home_meta_description', 'homeMetaDescription'),
            new StringField('home_keywords', 'homeKeywords'),
            (new CustomFields())->addFlags(new ApiAware()),
        ]);
    }
}
