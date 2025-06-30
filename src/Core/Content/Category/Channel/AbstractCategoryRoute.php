<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Category\Channel;

use HeyPanel\Core\System\Channel\ChannelContext;
use Symfony\Component\HttpFoundation\Request;

/**
 * This route can be used to a singled category with resolved cms page of the authenticated channel.
 * It is also possible to use "home" as navigationId to load the start page.
 */
abstract class AbstractCategoryRoute
{
    abstract public function getDecorated(): AbstractCategoryRoute;

    abstract public function load(string $navigationId, Request $request, ChannelContext $context): CategoryRouteResponse;
}
