<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme;

use HeyPanel\Core\System\Channel\ChannelContext;

abstract class AbstractResolvedConfigLoader
{
    abstract public function getDecorated(): AbstractResolvedConfigLoader;

    /**
     * @return array<string, mixed>
     */
    abstract public function load(string $themeId, ChannelContext $context): array;
}
