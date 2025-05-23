<?php

namespace ApiInsight\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('api_insight');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->booleanNode('enabled')
                    ->defaultTrue()
                    ->info('Activer ou désactiver le bundle')
                ->end()
                ->enumNode('storage')
                    ->values(['memory', 'redis', 'database'])
                    ->defaultValue('memory')
                    ->info('Type de stockage pour les métriques')
                ->end()
                ->arrayNode('auth')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultFalse()
                            ->info('Activer l\'authentification pour l\'endpoint /metrics')
                        ->end()
                        ->enumNode('type')
                            ->values(['token', 'jwt'])
                            ->defaultValue('token')
                            ->info('Type d\'authentification')
                        ->end()
                        ->scalarNode('token')
                            ->defaultNull()
                            ->info('Token d\'authentification')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('prometheus')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultFalse()
                            ->info('Activer l\'export au format Prometheus')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('dashboard')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultFalse()
                            ->info('Activer le dashboard web intégré')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
} 