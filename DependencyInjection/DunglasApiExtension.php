<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * The extension of this bundle.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class DunglasApiExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        if (empty($frameworkConfiguration = $container->getExtensionConfig('framework'))) {
            return;
        }

        if (!isset($frameworkConfiguration['serializer']) || !isset($frameworkConfiguration['serializer']['enabled'])) {
            $container->prependExtensionConfig('framework', ['serializer' => ['enabled' => true]]);
        }

        if (!isset($frameworkConfiguration['property_info']) || !isset($frameworkConfiguration['property_info']['enabled'])) {
            $container->prependExtensionConfig('framework', ['property_info' => ['enabled' => true]]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('api.title', $config['title']);
        $container->setParameter('api.description', $config['description']);
        $container->setParameter('api.supported_formats', $config['supported_formats']);
        $container->setParameter('api.collection.order', $config['collection']['order']);
        $container->setParameter('api.collection.order_parameter_name', $config['collection']['order_parameter_name']);
        $container->setParameter('api.collection.pagination.enabled', $config['collection']['pagination']['enabled']);
        $container->setParameter('api.collection.pagination.client_enabled', $config['collection']['pagination']['client_enabled']);
        $container->setParameter('api.collection.pagination.client_items_per_page', $config['collection']['pagination']['client_items_per_page']);
        $container->setParameter('api.collection.pagination.items_per_page', $config['collection']['pagination']['items_per_page']);
        $container->setParameter('api.collection.pagination.page_parameter_name', $config['collection']['pagination']['page_parameter_name']);
        $container->setParameter('api.collection.pagination.enabled_parameter_name', $config['collection']['pagination']['enabled_parameter_name']);
        $container->setParameter('api.collection.pagination.items_per_page_parameter_name', $config['collection']['pagination']['items_per_page_parameter_name']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('api.xml');
        $loader->load('metadata.xml');

        $this->enableJsonLd($loader);
        $this->registerAnnotationLoaders($container);
        $this->registerCache($config, $container, $loader);

        // Doctrine ORM support
        if (class_exists('Doctrine\ORM\Version')) {
            $loader->load('doctrine_orm.xml');
        }

        // FOSUser support
        if ($config['enable_fos_user']) {
            $loader->load('fos_user.xml');
        }
    }

    /**
     * Enables JSON-LD and Hydra support.
     *
     * @param XmlFileLoader $loader
     */
    private function enableJsonLd(XmlFileLoader $loader)
    {
        $loader->load('jsonld.xml');
        $loader->load('hydra.xml');
    }

    /**
     * Registers annotations loaders.
     *
     * @param ContainerBuilder $container
     */
    private function registerAnnotationLoaders(ContainerBuilder $container)
    {
        $paths = [];
        foreach ($container->getParameter('kernel.bundles') as $bundle) {
            $reflectionClass = new \ReflectionClass($bundle);
            $bundleDirectory = dirname($reflectionClass->getFileName());
            $entityDirectory = $bundleDirectory.DIRECTORY_SEPARATOR.'Entity';

            if (file_exists($entityDirectory)) {
                $paths[] = $entityDirectory;
            }
        }

        $container->getDefinition('api.metadata.resource.factory.collection.annotation')->addArgument($paths);
    }

    /**
     * Registers cache decorators.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     * @param XmlFileLoader    $loader
     */
    private function registerCache(array $config, ContainerBuilder $container, XmlFileLoader $loader)
    {
        if (!isset($config['cache']) || !$config['cache']) {
            return;
        }

        $loader->load('doctrine_cache.xml');

        $container->setParameter(
            'api.mapping.cache.prefix',
            'api_'.hash('sha256', $container->getParameter('kernel.root_dir'))
        );

        $cacheReference = new Reference($config['cache']);

        $container->getDefinition('api.mapping.resource.loader.collection.cache_decorator')->addArgument($cacheReference);
        $container->getDefinition('api.mapping.resource.loader.metadata.cache_decorator')->addArgument($cacheReference);
        $container->getDefinition('api.mapping.property.loader.collection.cache_decorator')->addArgument($cacheReference);
        $container->getDefinition('api.mapping.property.loader.metadata.cache_decorator')->addArgument($cacheReference);
    }
}
