<?php declare(strict_types=1);

namespace HeyPanel\Core\System;

use HeyPanel\Core\Framework\Bundle;
use HeyPanel\Core\System\DependencyInjection\CompilerPass\ChannelEntityCompilerPass;
use HeyPanel\Core\System\DependencyInjection\CompilerPass\NumberRangeIncrementerCompilerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @internal
 */
class System extends Bundle
{
    public function getTemplatePriority(): int
    {
        return -1;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/DependencyInjection/'));
        $loader->load('locale.xml');
        $loader->load('integration.xml');
        $loader->load('user.xml');
        $loader->load('configuration.xml');
        $loader->load('channel.xml');
        $loader->load('country.xml');
        $loader->load('currency.xml');
        $loader->load('customer.xml');
        $loader->load('number_range.xml');
        $loader->load('snippet.xml');
        $loader->load('tag.xml');
        $loader->load('state_machine.xml');

        $container->addCompilerPass(new ChannelEntityCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 0);
        $container->addCompilerPass(new NumberRangeIncrementerCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 0);
    }
}
