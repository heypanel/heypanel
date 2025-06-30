<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Post;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\IdField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;

class PostDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'post';
    final public const CONFIG_KEY_DEFAULT_CMS_PAGE_QUESTION = 'core.cms.default_post_cms_page';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return PostEntity::class;
    }

    public function getCollectionClass(): string
    {
        return PostCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),
        ]);
    }
}
