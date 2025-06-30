<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme;

use HeyPanel\Core\Framework\Plugin;
use HeyPanel\Core\Framework\Util\Filesystem;
use HeyPanel\Core\Kernel;
use HeyPanel\Frontend\Theme\FrontendPluginConfiguration\FrontendPluginConfiguration;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @internal
 */
class ThemeFilesystemResolver
{
    public function __construct(
        private readonly Kernel $kernel,
    ) {
    }

    public function getFilesystemForFrontendConfig(FrontendPluginConfiguration $configuration): Filesystem
    {
        try {
            $bundle = $this->kernel->getBundle($configuration->getTechnicalName());
        } catch (\InvalidArgumentException $e) {
            $bundles = $this->kernel->getPluginLoader()
                ->getPluginInstances()
                ->filter(fn (Plugin $plugin) => $plugin->getName() === $configuration->getTechnicalName())
                ->all();

            $bundle = array_values($bundles)[0];
        }

        \assert($bundle instanceof BundleInterface);

        return new Filesystem($bundle->getPath());
    }
}
