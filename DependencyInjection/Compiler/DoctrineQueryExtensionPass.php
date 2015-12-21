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
use Symfony\Component\DependencyInjection\Reference;

/**
 * Injects query extensions.
 *
 * @author Samuel ROZE <samuel.roze@gmail.com>
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class DoctrineQueryExtensionPass implements CompilerPassInterface
{
    use FindSortedServicesTrait;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $dataProviderDefinition = $container->getDefinition('api.doctrine.orm.data_provider');
        foreach ($this->findSortedServices($container, 'api.doctrine.orm.query_extension.item') as $extension) {
            $dataProviderDefinition->addMethodCall('addItemExtension', [$extension]);
        }
        foreach ($this->findSortedServices($container, 'api.doctrine.orm.query_extension.collection') as $extension) {
            $dataProviderDefinition->addMethodCall('addCollectionExtension', [$extension]);
        }
    }


}
