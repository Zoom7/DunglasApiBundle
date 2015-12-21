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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Find and sort services..
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
trait FindSortedServicesTrait
{
    /**
     * Finds services having the given tag and sorts them by their priority attribute.
     *
     * @param ContainerBuilder $container
     * @param string           $tag
     *
     * @return Reference[]
     */
    private function findSortedServices(ContainerBuilder $container, $tag)
    {
        $extensions = [];
        foreach ($container->findTaggedServiceIds($tag) as $serviceId => $tags) {
            foreach ($tags as $tag) {
                $priority = isset($tag['priority']) ? $tag['priority'] : 0;
                $extensions[$priority][] = new Reference($serviceId);
            }
        }
        krsort($extensions);

        // Flatten the array
        return empty($extensions) ? [] : call_user_func_array('array_merge', $extensions);
    }
}
