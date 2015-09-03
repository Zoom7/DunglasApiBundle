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

use Dunglas\ApiBundle\Mapping\Property\Loader\MetadataLoaderInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;

/**
 * Decorates a property loader. Sets readable and writeable links using Serializer metadata.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class MetadataLoaderDecorator implements MetadataLoaderInterface
{
    /**
     * @var MetadataLoaderInterface
     */
    private $loader;
    /**
     * @var ClassMetadataFactoryInterface
     */
    private $classMetadataFactory;

    public function __construct(MetadataLoaderInterface $loader, ClassMetadataFactoryInterface $classMetadataFactory, CollectionLoader)
    {
        $this->loader = $loader;
        $this->classMetadataFactory = $classMetadataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($resourceClass, $name, array $options)
    {
        $metadata = $this->loader->getMetadata($resourceClass, $name, $options);
        $normalizationGroups = $this->isGroupsSet($options['normalization_groups']);
        $denormalizationGroups = $this->isGroupsSet($options['denormalization_groups']);

        if (null === $metadata || (!$normalizationGroups && !$denormalizationGroups) || !$this->classMetadataFactory->getMetadataFor($resourceClass)) {
            return $metadata;
        }

        $groups = $this->getGroups();
        if (null === $groups) {
            return $metadata;
        }

        if (null === $metadata->isReadable()) {
            if ($this->isGroupsSet($options, 'normalization_groups')) {
                $metadata = $metadata->withReadable(true);
            } else {
                $metadata = count(array_intersect($groups, $options['normalization_groups'])) > 1;
            }
        }

        if (null === $metadata->isWritable()) {
            if ($this->isGroupsSet($options, 'denormalization_groups')) {
                $metadata = $metadata->withReadable(true);
            } else {
                $metadata = count(array_intersect($groups, $options['denormalization_groups'])) > 1;
            }
        }



        return $metadata;
    }

    /**
     * Tests if groups are set.
     *
     * @param array  $options
     * @param string $key
     *
     * @return bool
     */
    private function isGroupsSet(array $options, $key)
    {
        return isset($options[$key]) && is_array($options[$key]);
    }

    /**
     * Gets serialization groups for the given property.
     *
     * @param string $resourceClass
     * @param string $name
     *
     * @return string[]|null
     */
    private function getGroups($resourceClass, $name)
    {
        $serializerClassMetadata = $this->classMetadataFactory->getMetadataFor($resourceClass);
        foreach ($serializerClassMetadata->getAttributesMetadata() as $serializerAttributeMetadata) {
            if ($serializerAttributeMetadata->getName() === $name) {
                return $serializerAttributeMetadata->getGroups();
            }
        }
    }
}
