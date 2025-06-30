<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme;

use HeyPanel\Core\Framework\Struct\Collection;

/**
 * @extends Collection<ThemeChannel>
 */
class ThemeChannelCollection extends Collection
{
    protected function getExpectedClass(): string
    {
        return ThemeChannel::class;
    }
}
