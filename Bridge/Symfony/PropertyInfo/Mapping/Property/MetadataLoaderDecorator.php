<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Bridge\Symfony\PropertyInfo\Mapping;

use Dunglas\ApiBundle\Mapping\Property\Loader\Metadata\LoaderInterface;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;

/**
 * PropertyInfo metadata loader decorator.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class MetadataLoaderDecorator implements LoaderInterface
{
    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var PropertyInfoExtractorInterface
     */
    private $propertyInfo;

    public function __construct(LoaderInterface $loader, PropertyInfoExtractorInterface $propertyInfo)
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
        if (null === $metadata->getType()) {
            $types = $this->propertyInfo->getTypes($resourceClass, $name, $options);
            if (isset($types[0])) {
                $metadata = $metadata->withType($types);
            }
        }

        if (null === $metadata->getDescription())  {
            $metadata = $metadata->withDescription($this->propertyInfo->getShortDescription($resourceClass, $name, $options));
        }

        if (null === $metadata->isReadable()) {
            $metadata = $metadata->withReadable($this->propertyInfo->isReadable($resourceClass, $name, $options));
        }

        if (null === $metadata->isWritable()) {
            $metadata = $metadata->withWritable($this->propertyInfo->isWritable($resourceClass, $name, $options));
        }

        return $metadata;
    }
}
