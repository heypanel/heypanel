<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Frontend\Theme\FrontendPluginConfiguration\FrontendPluginConfiguration;
use HeyPanel\Frontend\Theme\FrontendPluginConfiguration\FrontendPluginConfigurationCollection;

interface ThemeCompilerInterface
{
    public function compileTheme(
        string $channelId,
        string $themeId,
        FrontendPluginConfiguration $themeConfig,
        FrontendPluginConfigurationCollection $configurationCollection,
        bool $withAssets,
        Context $context
    ): void;
}
