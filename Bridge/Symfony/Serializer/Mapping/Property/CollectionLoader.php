<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Bridge\Symfony\Serializer\Mapping\Property;

use Dunglas\ApiBundle\Mapping\Property\Collection;
use Dunglas\ApiBundle\Mapping\Property\Loader\CollectionLoaderInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;

/**
 * Loads properties from Serializer metadata (only applicable if groups are used).
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class CollectionLoader implements CollectionLoaderInterface
{
    /**
     * @var ClassMetadataFactoryInterface
     */
    private $classMetadataFactory;

    public function __construct(ClassMetadataFactoryInterface $classMetadataFactory)
    {
        $this->classMetadataFactory = $classMetadataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection($resourceClass, array $options)
    {
        if (!isset($options['normalization_groups']) && !isset($options['denormalization_groups'])) {
            return null;
        }

        if (!$this->classMetadataFactory->getMetadataFor($resourceClass)) {
            return new Collection();
        }

        $normalizationGroups = isset($options['normalization_groups']) ? $options['normalization_groups'] : [];
        $denormalizationGroups = isset($options['denormalization_groups']) ? $options['denormalization_groups'] : [];
        $groups = array_merge($normalizationGroups, $denormalizationGroups);

        $properties = [];

        $serializerClassMetadata = $this->classMetadataFactory->getMetadataFor($resourceClass);
        foreach ($serializerClassMetadata->getAttributesMetadata() as $serializerAttributeMetadata) {
            if (count(array_intersect($groups, $serializerAttributeMetadata->getGroups())) > 0) {
                $properties[] = $serializerAttributeMetadata->getName();
            }
        }

        return new Collection($properties);
    }
}
