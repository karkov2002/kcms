<?php

namespace Karkov\Kcms\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('kcms');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('zones')
                    ->children()
                        ->integerNode('nb')
                            ->defaultValue(10)
                            ->min(1)
                            ->max(20)
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->children()
                ->arrayNode('multilingual')
                    ->children()
                        ->scalarNode('enable')
                            ->defaultValue(false)
                            ->end()
                        ->scalarNode('default_local')
                            ->defaultValue(null)
                            ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
