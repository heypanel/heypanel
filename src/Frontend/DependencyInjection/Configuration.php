<?php declare(strict_types=1);

namespace HeyPanel\Frontend\DependencyInjection;

use HeyPanel\Frontend\Theme\ConfigLoader\DatabaseAvailableThemeProvider;
use HeyPanel\Frontend\Theme\ConfigLoader\DatabaseConfigLoader;
use HeyPanel\Frontend\Theme\SeedingThemePathBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('frontend');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('theme')
                    ->children()
                        ->scalarNode('config_loader_id')->defaultValue(DatabaseConfigLoader::class)->end()
                        ->scalarNode('theme_path_builder_id')->defaultValue(SeedingThemePathBuilder::class)->end()
                        ->scalarNode('available_theme_provider')->defaultValue(DatabaseAvailableThemeProvider::class)->end()
                        ->integerNode('file_delete_delay')
                            ->setDeprecated('HEYADMIN/frontend', '6.8.0', 'The "%node%" option is deprecated and will be removed in 6.8.0 as it has no effect anymore.')
                            ->defaultValue(900)->end()
                        ->arrayNode('allowed_scss_values')->performNoDeepMerging()
                            ->scalarPrototype()->end()
                        ->end()
                        ->booleanNode('validate_on_compile')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
