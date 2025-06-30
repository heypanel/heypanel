<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Media\Channel;

use HeyPanel\Core\System\Channel\ChannelContext;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractMediaRoute
{
    abstract public function getDecorated(): AbstractMediaRoute;

    abstract public function load(Request $request, ChannelContext $context): MediaRouteResponse;
}
