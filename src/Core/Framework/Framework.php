<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework;

use HeyPanel\Core\Framework\DataAbstractionLayer\AttributeEntityCompiler;
use HeyPanel\Core\Framework\DependencyInjection\CompilerPass\AssetBundleRegistrationCompilerPass;
use HeyPanel\Core\Framework\DependencyInjection\CompilerPass\AssetRegistrationCompilerPass;
use HeyPanel\Core\Framework\DependencyInjection\CompilerPass\AttributeEntityCompilerPass;
use HeyPanel\Core\Framework\DependencyInjection\CompilerPass\AutoconfigureCompilerPass;
use HeyPanel\Core\Framework\DependencyInjection\CompilerPass\DefaultTransportCompilerPass;
use HeyPanel\Core\Framework\DependencyInjection\CompilerPass\EntityCompilerPass;
use HeyPanel\Core\Framework\DependencyInjection\CompilerPass\FeatureFlagCompilerPass;
use HeyPanel\Core\Framework\DependencyInjection\CompilerPass\FilesystemConfigMigrationCompilerPass;
use HeyPanel\Core\Framework\DependencyInjection\CompilerPass\FrameworkMigrationReplacementCompilerPass;
use HeyPanel\Core\Framework\DependencyInjection\CompilerPass\MessengerMiddlewareCompilerPass;
use HeyPanel\Core\Framework\DependencyInjection\CompilerPass\RateLimiterCompilerPass;
use HeyPanel\Core\Framework\DependencyInjection\CompilerPass\TwigEnvironmentCompilerPass;
use HeyPanel\Core\Framework\DependencyInjection\FrameworkExtension;
use HeyPanel\Core\Framework\Increment\IncrementerGatewayCompilerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @internal
 */
class Framework extends Bundle
{
    public function getTemplatePriority(): int
    {
        return -1;
    }

    public function getContainerExtension(): Extension
    {
        return new FrameworkExtension();
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        $container->setParameter('locale', 'zh-CN');

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/DependencyInjection/'));
        $loader->load('services.xml');
        $loader->load('data-abstraction-layer.xml');
        $loader->load('filesystem.xml');
        $loader->load('message-queue.xml');
        $loader->load('plugin.xml');
        $loader->load('language.xml');
        $loader->load('custom-field.xml');
        $loader->load('increment.xml');
        $loader->load('scheduled-task.xml');
        $loader->load('health.xml');
        $loader->load('event.xml');
        $loader->load('rate-limiter.xml');
        $loader->load('acl.xml');
        $loader->load('api.xml');
        $loader->load('telemetry.xml');
        $loader->load('store.xml');
        $loader->load('flag.xml');
        $loader->load('cache.xml');
        $loader->load('rule.xml');
        $loader->load('notification.xml');
        $loader->load('seo.xml');
        $loader->load('hydrator.xml');

        if ($container->getParameter('kernel.environment') === 'test') {
            $loader->load('services_test.xml');
            $loader->load('store_test.xml');
            $loader->load('seo_test.xml');
        }
        $container->addCompilerPass(new AttributeEntityCompilerPass(new AttributeEntityCompiler()), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1000);
        $container->addCompilerPass(new FeatureFlagCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1000);

        $container->addCompilerPass(new EntityCompilerPass());
        $container->addCompilerPass(new MessengerMiddlewareCompilerPass());
        $container->addCompilerPass(new DefaultTransportCompilerPass());
        $container->addCompilerPass(new TwigEnvironmentCompilerPass());

        $container->addCompilerPass(new FilesystemConfigMigrationCompilerPass());
        $container->addCompilerPass(new IncrementerGatewayCompilerPass());
        $container->addCompilerPass(new RateLimiterCompilerPass());
        $container->addCompilerPass(new AssetBundleRegistrationCompilerPass());
        $container->addCompilerPass(new AssetRegistrationCompilerPass());
        $container->addCompilerPass(new AutoconfigureCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1000);

        $container->addCompilerPass(new FrameworkMigrationReplacementCompilerPass());

        parent::build($container);
        $this->buildDefaultConfig($container);
    }
}
