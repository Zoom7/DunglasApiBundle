<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Bridge\Symfony\PropertyInfo\Metadata\Property;

use Dunglas\ApiBundle\Metadata\Property\Factory\ItemMetadataFactoryInterface;
use Dunglas\ApiBundle\Metadata\Property\ItemMetadata;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;

/**
 * PropertyInfo metadata loader decorator.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class ItemMetadataFactory implements ItemMetadataFactoryInterface
{
    /**
     * @var PropertyInfoExtractorInterface
     */
    private $propertyInfo;

    /**
     * @var ItemMetadataFactoryInterface
     */
    private $decorated;

    public function __construct(PropertyInfoExtractorInterface $propertyInfo, ItemMetadataFactoryInterface $decorated)
    {
        $this->propertyInfo = $propertyInfo;
        $this->decorated = $decorated;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $resourceClass, string $name, array $options) : ItemMetadata
    {
        $itemMetadata = $this->decorated->create($resourceClass, $name, $options);
        if (null === $itemMetadata->getType()) {
            $types = $this->propertyInfo->getTypes($resourceClass, $name, $options);
            if (isset($types[0])) {
                $itemMetadata = $itemMetadata->withType($types);
            }
        }

        if (null === $itemMetadata->getDescription()) {
            $itemMetadata = $itemMetadata->withDescription($this->propertyInfo->getShortDescription($resourceClass, $name, $options));
        }

        if (null === $itemMetadata->isReadable()) {
            $itemMetadata = $itemMetadata->withReadable($this->propertyInfo->isReadable($resourceClass, $name, $options));
        }

        if (null === $itemMetadata->isWritable()) {
            $itemMetadata = $itemMetadata->withWritable($this->propertyInfo->isWritable($resourceClass, $name, $options));
        }

        return $itemMetadata;
    }
}
