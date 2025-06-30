<?php declare(strict_types=1);

namespace HeyPanel\Core\Profiling\Routing;

use HeyPanel\Core\Framework\Routing\RouteScopeWhitelistInterface;
use HeyPanel\Core\Profiling\Controller\ProfilerController;

class ProfilerWhitelist implements RouteScopeWhitelistInterface
{
    public function applies(string $controllerClass): bool
    {
        return $controllerClass === ProfilerController::class;
    }
}
