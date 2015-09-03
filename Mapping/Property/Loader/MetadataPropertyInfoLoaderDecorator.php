<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Mapping\Property\Loader;

use PropertyInfo\PropertyInfoInterface;

/**
 * Property info extractor decorator.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class MetadataPropertyInfoLoaderDecorator implements MetadataLoaderInterface
{
    /**
     * @var MetadataLoaderInterface
     */
    private $loader;
    /**
     * @var PropertyInfoInterface
     */
    private $propertyInfo;

    public function __construct(MetadataLoaderInterface $loader, PropertyInfoInterface $propertyInfo)
    {
        $this->loader = $loader;
        $this->propertyInfo = $propertyInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($resourceClass, $name, array $options)
    {
        $metadata = $this->loader->getMetadata($resourceClass, $name, $options);
        $reflectionClass = new \ReflectionClass($resourceClass);
        if (null === $metadata) {
            return $metadata;
        }

        if (!$reflectionClass->hasProperty($name)) {
            return $metadata;
        }

        $reflectionProperty = $reflectionClass->getProperty($name);

        if (null === $metadata->getType()) {
            $types = $this->propertyInfo->getTypes($reflectionProperty);
            if (isset($types[0])) {
                $metadata = $metadata->withType($types);
            }
        }

        if (null === $metadata->getDescription())  {
            $metadata = $metadata->withDescription($this->propertyInfo->getShortDescription($reflectionProperty));
        }

        return $metadata;
    }
}
