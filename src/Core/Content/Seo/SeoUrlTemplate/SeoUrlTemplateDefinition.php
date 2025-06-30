<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Seo\SeoUrlTemplate;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\BoolField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\FkField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\AllowEmptyString;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\IdField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\StringField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;
use HeyPanel\Core\System\Channel\ChannelDefinition;

class SeoUrlTemplateDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'seo_url_template';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return SeoUrlTemplateEntity::class;
    }

    public function getCollectionClass(): string
    {
        return SeoUrlTemplateCollection::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('channel_id', 'channelId', ChannelDefinition::class))->addFlags(new ApiAware()),

            (new StringField('entity_name', 'entityName', 64))->addFlags(new Required()),
            (new StringField('route_name', 'routeName'))->addFlags(new Required()),
            (new StringField('template', 'template', 750))->addFlags(new AllowEmptyString()),
            (new BoolField('is_valid', 'isValid'))->addFlags(new ApiAware()),
            (new CustomFields())->addFlags(new ApiAware()),
            new ManyToOneAssociationField('channel', 'channel_id', ChannelDefinition::class, 'id', false),
        ]);
    }
}
