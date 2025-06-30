<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Category\Service;

use HeyPanel\Core\Content\Category\Tree\Tree;
use HeyPanel\Core\System\Channel\ChannelContext;

interface NavigationLoaderInterface
{
    /**
     * Returns the first two levels of the category tree, as well as all parents of the active category
     * and the active categories first level of children.
     * The provided active id will be marked as selected
     */
    public function load(string $activeId, ChannelContext $context, string $rootId, int $depth = 2): Tree;
}
