<?php declare(strict_types=1);

namespace HeyPanel\Administration\Framework\Routing\KnownIps;

use Symfony\Component\HttpFoundation\Request;

interface KnownIpsCollectorInterface
{
    /**
     * @return array<string, string>
     */
    public function collectIps(Request $request): array;
}
