<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\JsonLd\Serializer;

/**
 * Serializer context creation and manipulation.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
trait ContextTrait
{
    /**
     * Creates normalization context.
     *
     * @param string $resourceClass
     * @param array  $context
     *
     * @return array
     */
    private function createContext(string $resourceClass, array $context) : array
    {
        if (isset($context['jsonld_has_context'])) {
            return $context;
        }

        return $context + [
            'resource_class' => $resourceClass,
            'jsonld_has_context' => true,
            // Don't use hydra:Collection in sub levels
            'jsonld_sub_level' => true,
            'jsonld_normalization_groups' => $resource->getNormalizationGroups(),
            'jsonld_denormalization_groups' => $resource->getDenormalizationGroups(),
            'jsonld_validation_groups' => $resource->getValidationGroups(),
        ];
    }

    /**
     * Creates relation context.
     *
     * @param string $resourceClass
     * @param array  $context
     *
     * @return array
     */
    private function createRelationContext(string $resourceClass, array $context) : array
    {
        $context['resource_class'] = $resourceClass;
        unset($context['object_to_populate']);

        return $context;
    }
}
