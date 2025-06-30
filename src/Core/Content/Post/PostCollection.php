<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Post;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<PostEntity>
 */
class PostCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'post_collection';
    }

    protected function getExpectedClass(): string
    {
        return PostEntity::class;
    }
}
