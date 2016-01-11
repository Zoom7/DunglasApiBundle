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
 * Injects filters.
 *
 * @internal
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class FilterPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $filters = [];
        foreach ($container->findTaggedServiceIds('api.filter') as $serviceId => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['name'])) {
                    throw new \RuntimeException('Filter tags must have a "name" property.');
                }

                $filters[$tag['name']] = new Reference($serviceId);
            }
        }

        $container->getDefinition('api.filters')->addArgument($filters);
    }
}
