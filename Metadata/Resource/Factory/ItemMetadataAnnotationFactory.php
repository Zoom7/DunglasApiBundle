<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Metadata\Resource\Factory;

use Doctrine\Common\Annotations\Reader;
use Dunglas\ApiBundle\Annotation\Resource;
use Dunglas\ApiBundle\Exception\ResourceClassNotFoundException;
use Dunglas\ApiBundle\Metadata\Resource\ItemMetadata;

/**
 * Parses Resource annotation and create an item metadata.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class ItemMetadataAnnotationFactory implements ItemMetadataFactoryInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var ItemMetadataFactoryInterface|null
     */
    private $decorated;

    public function __construct(Reader $reader, ItemMetadataFactoryInterface $decorated = null)
    {
        $this->reader = $reader;
        $this->decorated = $decorated;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $resourceClass) : ItemMetadata
    {
        $parentItemMetadata = null;
        if ($this->decorated) {
            try {
                $parentItemMetadata = $this->decorated->create($resourceClass);
            } catch (ResourceClassNotFoundException $resourceNotFoundException) {
                // Ignore not found exception from decorated factories
            }
        }

        try {
            $reflectionClass = new \ReflectionClass($resourceClass);
        } catch (\ReflectionException $reflectionException) {
            return $this->handleNotFound($parentItemMetadata, $resourceClass);
        }

        $resourceAnnotation = $this->reader->getClassAnnotation($reflectionClass, Resource::class);
        if (null === $resourceAnnotation) {
            return $this->handleNotFound($parentItemMetadata, $resourceClass);
        }

        return $this->createMetadata($resourceAnnotation, $parentItemMetadata);
    }

    /**
     * Returns the metadata from the decorated factory if available or throws an exception.
     *
     * @param ItemMetadata|null $parentMetadata
     * @param string $resourceClass
     *
     * @return ItemMetadata
     *
     * @throws ResourceClassNotFoundException
     */
    private function handleNotFound(ItemMetadata $parentMetadata = null, string $resourceClass) : ItemMetadata
    {
        if (null !== $parentMetadata) {
            return $parentMetadata;
        }

        throw new ResourceClassNotFoundException(sprintf('Resource "%s" not found.', $resourceClass));
    }

    private function createMetadata(Resource $annotation, ItemMetadata $parentItemMetadata = null) : ItemMetadata
    {
        if (!$parentItemMetadata) {
            return new ItemMetadata(
                $annotation->shortName,
                $annotation->description,
                $annotation->iri,
                $annotation->itemOperations,
                $annotation->collectionOperations,
                $annotation->attributes
            );
        }

        $itemMetadata = $parentItemMetadata;
        foreach (['shortName', 'description', 'iri', 'itemOperations', 'collectionOperations', 'attributes'] as $property) {
            $this->createWith($itemMetadata, $property, $annotation->$property);
        }

        return $itemMetadata;
    }

    private function createWith(ItemMetadata $itemMetadata, string $property, $value) : ItemMetadata
    {
        $ucfirstedProperty = ucfirst($property);
        $getter = 'get'.$ucfirstedProperty;

        if (null !== $itemMetadata->$getter()) {
            return $itemMetadata;
        }

        $wither = 'with'.$ucfirstedProperty;

        return $itemMetadata->$wither($value);
    }
}
