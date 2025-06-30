<?php
declare(strict_types=1);

namespace HeyPanel\Frontend\Theme\ConfigLoader;

use HeyPanel\Core\Framework\Context;

abstract class AbstractAvailableThemeProvider
{
    abstract public function getDecorated(): AbstractAvailableThemeProvider;

    /**
     * @return array<string, string>
     */
    abstract public function load(Context $context, bool $activeOnly): array;
}
