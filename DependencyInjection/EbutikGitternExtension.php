<?php

namespace Ebutik\GitternBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

class EbutikGitternExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('gittern.repository.transport.real.path', $config['git_dir']);

        $outer_transport_service = new Reference('gittern.repository.transport.real');

        if (isset($config['cache']) && $config['cache']['service'] !== null)
        {
            $def = $container->getDefinition('gittern.repository.transport.cache');
            $def->replaceArgument(0, $outer_transport_service);
            $def->replaceArgument(1, new Reference($config['cache']['service']));
            $def->replaceArgument(2, $config['cache']['ttl']);

            $outer_transport_service = new Reference('gittern.repository.transport.cache');
        }

        if ($config['profiling'])
        {
            $def = $container->getDefinition('gittern.repository.transport.profiler');
            $def->replaceArgument(0, $outer_transport_service);

            $outer_transport_service = new Reference('gittern.repository.transport.profiler');
        }
        else
        {
            $container->removeDefinition('gittern.data_collector.transport');
        }

        $container->setAlias('gittern.repository.transport', (string)$outer_transport_service);
    }
}