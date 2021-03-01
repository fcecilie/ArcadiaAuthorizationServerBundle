<?php

namespace Arcadia\Bundle\AuthorizationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('arcadia_authorization');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()

                ->scalarNode('keys_path')
                    ->info('This value define the path where keys will be stored.')
                    ->defaultValue('%kernel.project_dir%/config/authorization')
                ->end()

                ->arrayNode('servers')
                    ->info('The servers to which you will send your request with authorization.')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('username')
                                ->isRequired()
                            ->end()
                            ->scalarNode('password')
                                ->isRequired()
                            ->end()
                            ->scalarNode('ttl')
                                ->defaultValue('+3 min')
                            ->end()
                        ->end()
                    ->end()
                ->end()

            ->end()
        ;

        return $treeBuilder;
    }
}