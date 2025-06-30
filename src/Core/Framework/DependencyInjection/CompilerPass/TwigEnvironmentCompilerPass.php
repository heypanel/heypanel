<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DependencyInjection\CompilerPass;

use HeyPanel\Core\Framework\Adapter\Twig\TwigEnvironment;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @codeCoverageIgnore This would be useless as a unit test. It is integration tested here: \HeyPanel\Tests\Integration\Core\Framework\DependencyInjection\CompilerPass\TwigEnvironmentCompilerPassTest
 */
class TwigEnvironmentCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $twigEnvironment = $container->findDefinition('twig');
        // symfony service subscriber somehow don't work, therefore the service has to be public
        $twigEnvironment->setPublic(true);
        $twigEnvironment->setClass(TwigEnvironment::class);

        // The twig extension directly compiles the config into the service, there is no other way to get it @see \Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension::load
        $twigOptions = $twigEnvironment->getArgument(1);
        \assert(\is_array($twigOptions));

        $configuredTwigCache = $twigOptions['cache'] ?? false;
        if (!\is_string($configuredTwigCache)) {
            $container->setParameter('twig.cache', $container->getParameter('kernel.cache_dir') . '/twig');

            return;
        }

        $container->setParameter('twig.cache', $configuredTwigCache);
    }
}
