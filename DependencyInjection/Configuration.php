<?php

namespace Ebutik\GitternBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ebutik_gittern');

        $rootNode
            ->children()
                ->scalarNode('git_dir')
//                    ->info('The path to the .git directory to access.')
                ->end()
                ->scalarNode('profiling')
//                    ->info('Enable profiling')
                    ->defaultValue(true)
                ->end()
                ->arrayNode('cache')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('service')->defaultValue(null)->end()
                        ->scalarNode('ttl')->defaultValue(3600)->end()
                    ->end()
//                    ->info('Service ID of the Doctrine Cache service to use (or null if none)')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}