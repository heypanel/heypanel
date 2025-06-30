<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Post\Aggregate\PostCategory;

use HeyPanel\Core\Content\Category\CategoryDefinition;
use HeyPanel\Core\Content\Post\PostDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\FkField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;
use HeyPanel\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;

class PostCategoryDefinition extends MappingEntityDefinition
{
    final public const ENTITY_NAME = 'post_category';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('post_id', 'postId', PostDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            (new ReferenceVersionField(PostDefinition::class))->addFlags(new PrimaryKey(), new Required()),

            (new FkField('category_id', 'categoryId', CategoryDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            (new ReferenceVersionField(CategoryDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            new ManyToOneAssociationField('post', 'post_id', PostDefinition::class, 'id', false),
            new ManyToOneAssociationField('category', 'category_id', CategoryDefinition::class, 'id', false),
        ]);
    }
}
