<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Adapter\Kernel;

use Composer\Autoload\ClassLoader;
use Composer\InstalledVersions;
use Doctrine\DBAL\Connection;
use HeyPanel\Core\DevOps\Environment\EnvironmentHelper;
use HeyPanel\Core\Framework\Adapter\Database\MySQLFactory;
use HeyPanel\Core\Framework\Plugin\KernelPluginLoader\DbalKernelPluginLoader;
use HeyPanel\Core\Framework\Plugin\KernelPluginLoader\KernelPluginLoader;
use HeyPanel\Core\Kernel;
use HeyPanel\Core\Profiling\Doctrine\ProfilingMiddleware;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * HeyPanel\Core\Framework\Adapter\Kernel\KernelFactory
 *      HeyPanel\Core\Kernel
 *          HeyPanel\Core\Framework\Adapter\Kernel\HttpCacheKernel (http caching)
 *              HeyPanel\Core\Framework\Adapter\Kernel\HttpKernel (runs request transformer)
 *                  HeyPanel\Storefront\Controller\Any
 *
 * @final
 */
class KernelFactory
{
    /**
     * @var class-string<Kernel>
     */
    public static string $kernelClass = Kernel::class;

    public static function create(
        string $environment,
        bool $debug,
        ClassLoader $classLoader,
        ?KernelPluginLoader $pluginLoader = null,
        ?Connection $connection = null
    ): HttpKernelInterface {
        if (InstalledVersions::isInstalled('heypanel/platform')) {
            $heypanelVersion = InstalledVersions::getVersion('heypanel/platform')
                . '@' . InstalledVersions::getReference('heypanel/platform');
        } else {
            $heypanelVersion = InstalledVersions::getVersion('heypanel/core')
                . '@' . InstalledVersions::getReference('heypanel/core');
        }

        $middlewares = [];
        if ((\PHP_SAPI !== 'cli' || \in_array('--profile', $_SERVER['argv'] ?? [], true))
            && $environment !== 'prod' && InstalledVersions::isInstalled('symfony/doctrine-bridge')) {
            $middlewares = [new ProfilingMiddleware()];
        }

        $connection = $connection ?? MySQLFactory::create($middlewares);

        $pluginLoader = $pluginLoader ?? new DbalKernelPluginLoader($classLoader, null, $connection);

        $cacheId = (string) EnvironmentHelper::getVariable('SHOPWARE_CACHE_ID', '');

        /** @var KernelInterface $kernel */
        $kernel = new static::$kernelClass(
            $environment,
            $debug,
            $pluginLoader,
            $cacheId,
            $heypanelVersion,
            $connection,
            self::getProjectDir()
        );

        return $kernel;
    }

    private static function getProjectDir(): string
    {
        if ($dir = $_ENV['PROJECT_ROOT'] ?? $_SERVER['PROJECT_ROOT'] ?? false) {
            return $dir;
        }

        $r = new \ReflectionClass(self::class);

        /** @var string $dir */
        $dir = $r->getFileName();

        $dir = $rootDir = \dirname($dir);
        while (!\is_dir($dir . '/vendor')) {
            if ($dir === \dirname($dir)) {
                return $rootDir;
            }
            $dir = \dirname($dir);
        }

        return $dir;
    }
}
