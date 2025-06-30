<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Adapter\Twig\NamespaceHierarchy;

use HeyPanel\Core\Framework\Bundle;
use Symfony\Component\HttpKernel\KernelInterface;

class BundleHierarchyBuilder implements TemplateNamespaceHierarchyBuilderInterface
{
    /**
     * @internal
     */
    public function __construct(
        private readonly KernelInterface $kernel
    ) {
    }

    public function buildNamespaceHierarchy(array $namespaceHierarchy): array
    {
        $bundles = [];

        foreach ($this->kernel->getBundles() as $bundle) {
            if (!$bundle instanceof Bundle) {
                continue;
            }

            $bundlePath = $bundle->getPath();

            $directory = $bundlePath . '/Resources/views';

            if (!file_exists($directory)) {
                continue;
            }

            $bundles[$bundle->getName()] = $bundle->getTemplatePriority();
        }
        /** @var list<string> $bundles */
        $bundles = array_reverse($bundles);

        asort($bundles);

        return array_merge(
            $bundles,
            $namespaceHierarchy
        );
    }
}
