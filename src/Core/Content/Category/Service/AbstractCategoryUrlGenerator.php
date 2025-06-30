<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Category\Service;

use HeyPanel\Core\Content\Category\CategoryEntity;
use HeyPanel\Core\System\Channel\ChannelEntity;

abstract class AbstractCategoryUrlGenerator
{
    abstract public function getDecorated(): AbstractCategoryUrlGenerator;

    abstract public function generate(CategoryEntity $category, ?ChannelEntity $channel): ?string;
}
