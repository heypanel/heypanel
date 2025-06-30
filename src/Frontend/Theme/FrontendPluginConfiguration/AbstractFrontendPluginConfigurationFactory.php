<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme\FrontendPluginConfiguration;

use HeyPanel\Core\Framework\Bundle;

abstract class AbstractFrontendPluginConfigurationFactory
{
    abstract public function getDecorated(): AbstractFrontendPluginConfigurationFactory;

    abstract public function createFromBundle(Bundle $bundle): FrontendPluginConfiguration;

    /**
     * @param array<string, mixed> $data
     */
    abstract public function createFromThemeJson(string $name, array $data, string $path): FrontendPluginConfiguration;
}
