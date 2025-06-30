<?php declare(strict_types=1);

namespace HeyPanel\Frontend;

use HeyPanel\Core\Framework\Bundle;
use HeyPanel\Frontend\DependencyInjection\FrontendMigrationReplacementCompilerPass;
use HeyPanel\Frontend\Framework\ThemeInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @internal
 */
class Frontend extends Bundle implements ThemeInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $this->buildDefaultConfig($container);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/DependencyInjection'));
        $loader->load('services.xml');
        $loader->load('theme.xml');

        $container->setParameter('frontendRoot', $this->getPath());
        $container->addCompilerPass(new FrontendMigrationReplacementCompilerPass());
    }
}
