<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FilesystemConfigMigrationCompilerPass implements CompilerPassInterface
{
    private const MIGRATED_FS = ['theme', 'asset', 'sitemap'];

    public function process(ContainerBuilder $container): void
    {
        foreach (self::MIGRATED_FS as $fs) {
            $key = \sprintf('heypanel.filesystem.%s', $fs);
            $urlKey = $key . '.url';
            $typeKey = $key . '.type';
            $configKey = $key . '.config';
            if ($container->hasParameter($typeKey)) {
                continue;
            }

            // 6.1 always refers to the main shop url on theme, asset and sitemap.
            $container->setParameter($urlKey, '');
            $container->setParameter($key, '%heypanel.filesystem.public%');
            $container->setParameter($typeKey, '%heypanel.filesystem.public.type%');
            $container->setParameter($configKey, '%heypanel.filesystem.public.config%');
        }

        if (!$container->hasParameter('heypanel.filesystem.public.url')) {
            $container->setParameter('heypanel.filesystem.public.url', '%heypanel.cdn.url%');
        }
    }
}
