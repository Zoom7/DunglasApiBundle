<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Registers metadata loaders.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class MappingLoaderPass implements CompilerPassInterface
{
    use FindSortedServicesTrait;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->registerLoaders($container, 'api.mapping.resource.loader.collection');
        $this->registerLoaders($container, 'api.mapping.resource.loader.metadata');
        $this->registerLoaders($container, 'api.mapping.property.loader.collection');
        $this->registerLoaders($container, 'api.mapping.property.loader.metadata');
    }

    private function registerLoaders(ContainerBuilder $container, $tagName) {
        $resourceCollectionLoaderDefinition = $container->getDefinition($tagName.'.chain');

        $resourceCollectionLoaders = [];
        foreach ($this->findSortedServices($container, $tagName) as $resourceCollectionLoader) {
            $resourceCollectionLoaders[] = $resourceCollectionLoader;
        }

        $resourceCollectionLoaderDefinition->addArgument($resourceCollectionLoaders);
    }
}
