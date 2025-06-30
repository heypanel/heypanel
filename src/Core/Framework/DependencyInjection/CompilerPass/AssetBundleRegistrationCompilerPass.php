<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DependencyInjection\CompilerPass;

use HeyPanel\Core\Framework\Adapter\Asset\AssetPackageService;
use HeyPanel\Core\Framework\Bundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AssetBundleRegistrationCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /** @var array<class-string<Bundle>> $bundles */
        $bundles = $container->getParameter('kernel.bundles');

        $assetService = $container->getDefinition('assets.packages');
        $assetService->setFactory([AssetPackageService::class, 'create']);

        $bundleMap = [];

        foreach ($bundles as $bundleClass) {
            $reflection = new \ReflectionClass($bundleClass);
            $bundle = $reflection->newInstanceWithoutConstructor();

            if ($bundle instanceof Bundle) {
                $bundleMap[$bundle->getName()] = $bundle->getPath();
            }
        }

        $arguments = $assetService->getArguments();
        array_unshift($arguments, new Reference('heypanel.asset.public.version_strategy'));
        array_unshift($arguments, new Reference('heypanel.asset.asset_without_versioning'));
        array_unshift($arguments, $bundleMap);

        $assetService->setArguments($arguments);
    }
}
