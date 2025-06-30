<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Aggregate\ChannelTypeTranslation;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\StringField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;
use HeyPanel\Core\System\Channel\Aggregate\ChannelType\ChannelTypeDefinition;

class ChannelTypeTranslationDefinition extends EntityTranslationDefinition
{
    final public const ENTITY_NAME = 'channel_type_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return ChannelTypeTranslationCollection::class;
    }

    public function getEntityClass(): string
    {
        return ChannelTypeTranslationEntity::class;
    }

    protected function getParentDefinitionClass(): string
    {
        return ChannelTypeDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))->addFlags(new Required()),
            new StringField('description', 'description'),
            (new LongTextField('description_long', 'descriptionLong'))->addFlags(new ApiAware(), new AllowHtml()),
            new CustomFields(),
        ]);
    }
}
