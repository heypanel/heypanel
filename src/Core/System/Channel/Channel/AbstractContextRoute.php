<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Channel;

use HeyPanel\Core\System\Channel\ChannelContext;

/**
 * This route can be used to fetch the current context
 * The context contains information about the logged-in user, selected language, selected address etc.
 */
abstract class AbstractContextRoute
{
    abstract public function getDecorated(): AbstractContextRoute;

    abstract public function load(ChannelContext $context): ContextLoadRouteResponse;
}
