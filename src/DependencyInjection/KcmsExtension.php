<?php

namespace Karkov\Kcms\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

class KcmsExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // set autowiring and autoconfig on full bundle classes
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        // set default config...
        $defaultConfig = Yaml::parseFile(__DIR__.'/../Resources/config/kcms.yaml');

        // ...override by package config
        $configuration = $this->getConfiguration($configs, $container);
        $processedConfig = $this->processConfiguration($configuration, [$defaultConfig['kcms'], $configs[0]]);
        $container->setParameter('kcms', $processedConfig);
    }

    public function prepend(ContainerBuilder $container)
    {
        // twig config
        $container->loadFromExtension('twig', ['paths' => $this->getTwigPaths()]);
    }

    private function getTwigPaths()
    {
        $paths = [];

        // Default bundle templates
        $paths['%kernel.project_dir%/vendor/karkov/kcms-bundle/src/Resources/views/'] = 'KcmsBundle';

        return $paths;
    }
}
