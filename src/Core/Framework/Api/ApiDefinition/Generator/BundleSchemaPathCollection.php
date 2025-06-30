<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Api\ApiDefinition\Generator;

use HeyPanel\Core\Framework\Api\ApiDefinition\DefinitionService;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @internal
 *
 * @phpstan-import-type Api from DefinitionService
 */
class BundleSchemaPathCollection
{
    /**
     * @param iterable<Bundle> $bundles
     */
    public function __construct(private readonly iterable $bundles)
    {
    }

    /**
     * @param Api $api
     *
     * @return string[]
     */
    public function getSchemaPaths(string $api, ?string $bundleName): array
    {
        $apiFolder = $api === DefinitionService::API ? 'AdminApi' : 'ClientApi';
        $openApiDirs = [];
        foreach ($this->bundles as $bundle) {
            $path = $bundle->getPath() . '/Resources/Schema/' . $apiFolder;
            if (!is_dir($path)) {
                continue;
            }
            $openApiDirs[] = $path;
            if ($bundle->getName() === $bundleName) {
                unset($openApiDirs);
                $openApiDirs[] = $path;

                break;
            }
        }

        return $openApiDirs;
    }
}
