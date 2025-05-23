<?php

namespace ApiInsight\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ApiInsightExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('api_insight.storage', $config['storage']);
        $container->setParameter('api_insight.enabled', $config['enabled']);
        $container->setParameter('api_insight.auth_enabled', $config['auth']['enabled']);
        $container->setParameter('api_insight.auth_type', $config['auth']['type']);
        $container->setParameter('api_insight.auth_token', $config['auth']['token']);
        $container->setParameter('api_insight.prometheus_enabled', $config['prometheus']['enabled']);
        $container->setParameter('api_insight.dashboard_enabled', $config['dashboard']['enabled']);
        
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }
} 