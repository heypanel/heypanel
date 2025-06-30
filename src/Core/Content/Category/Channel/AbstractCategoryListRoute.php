<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Category\Channel;

use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\System\Channel\ChannelContext;

abstract class AbstractCategoryListRoute
{
    abstract public function getDecorated(): AbstractCategoryListRoute;

    abstract public function load(Criteria $criteria, ChannelContext $context): CategoryListRouteResponse;
}
