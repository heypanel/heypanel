<?php declare(strict_types=1);

namespace HeyPanel\Core\Profiling\Integration;

/**
 * @internal experimental atm
 */
interface ProfilerInterface
{
    public function start(string $title, string $category, array $tags): void;

    public function stop(string $title): void;
}
