<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\LandingPage\Channel;

use HeyPanel\Core\System\Channel\ChannelContext;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractLandingPageRoute
{
    abstract public function getDecorated(): AbstractLandingPageRoute;

    abstract public function load(string $landingPageId, Request $request, ChannelContext $context): LandingPageRouteResponse;
}
