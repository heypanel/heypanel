<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme\ConfigLoader;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Frontend\Theme\FrontendPluginConfiguration\FrontendPluginConfiguration;

abstract class AbstractConfigLoader
{
    abstract public function getDecorated(): AbstractConfigLoader;

    abstract public function load(string $themeId, Context $context): FrontendPluginConfiguration;
}
