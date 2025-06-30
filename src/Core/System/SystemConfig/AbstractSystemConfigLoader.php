<?php declare(strict_types=1);

namespace HeyPanel\Core\System\SystemConfig;

abstract class AbstractSystemConfigLoader
{
    abstract public function getDecorated(): AbstractSystemConfigLoader;

    abstract public function load(?string $channelId): array;
}
