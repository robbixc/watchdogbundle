<?php

namespace Kcs\WatchdogBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

class KcsWatchdogExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container) {

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('kcs_watchdog.error_reporting_level', $config['error_reporting_level']);
        $container->setParameter('kcs_watchdog.allowed_exceptions', $config['allowed_exceptions']);
        $container->setParameter('kcs_watchdog.ignored_errors_path', $config['ignored_errors_path']);
        $container->setParameter('kcs_watchdog.document_manager', $config['document_manager']);
        $loader = new XmlFileLoader(
                $container,
                new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load($config['db_driver'] . '.xml');
        $loader->load('services.xml');
    }
}
