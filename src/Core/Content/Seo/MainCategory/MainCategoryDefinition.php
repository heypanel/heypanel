<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Seo\MainCategory;

use HeyPanel\Core\Content\Category\CategoryDefinition;
use HeyPanel\Core\Content\Post\PostDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\FkField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\IdField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;
use HeyPanel\Core\System\Channel\ChannelDefinition;

class MainCategoryDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'main_category';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return MainCategoryCollection::class;
    }

    public function getEntityClass(): string
    {
        return MainCategoryEntity::class;
    }

    public function isInheritanceAware(): bool
    {
        return false;
    }

    public function isVersionAware(): bool
    {
        return false;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),

            (new FkField('post_id', 'questionId', PostDefinition::class))->addFlags(new ApiAware(), new Required()),
            (new ReferenceVersionField(PostDefinition::class))->addFlags(new ApiAware(), new Required()),

            (new FkField('category_id', 'categoryId', CategoryDefinition::class))->addFlags(new ApiAware(), new Required()),
            (new ReferenceVersionField(CategoryDefinition::class))->addFlags(new ApiAware(), new Required()),
            (new FkField('channel_id', 'channelId', ChannelDefinition::class))->addFlags(new ApiAware(), new Required()),
            new ManyToOneAssociationField('question', 'post_id', PostDefinition::class),
            new ManyToOneAssociationField('category', 'category_id', CategoryDefinition::class),
            new ManyToOneAssociationField('channel', 'channel_id', ChannelDefinition::class),
        ]);
    }
}
