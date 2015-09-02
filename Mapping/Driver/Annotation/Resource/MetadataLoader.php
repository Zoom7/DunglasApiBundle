<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Mapping\Driver\Annotation\Resource;

use Doctrine\Common\Annotations\Reader;
use Dunglas\ApiBundle\Annotation\Iri;
use Dunglas\ApiBundle\Annotation\Resource;
use Dunglas\ApiBundle\Annotation\Operation as OperationAnnotation;
use Dunglas\ApiBundle\Mapping\Resource\PaginationSettings;
use Dunglas\ApiBundle\Mapping\Resource\Operation;
use Dunglas\ApiBundle\Mapping\Resource\Metadata;
use Dunglas\ApiBundle\Mapping\Resource\Loader\MetadataLoaderInterface;
use Dunglas\ApiBundle\Mapping\Util\ShortNameGuesser;

/**
 * Parses Resource annotation.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class ResourceClassMetadataLoader implements MetadataLoaderInterface
{
    /**
     * @var Reader
     */
    private $reader;
    /**
     * @var ShortNameGuesser
     */
    private $shortNameGuesser;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
        $this->shortNameGuesser = new ShortNameGuesser();
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($name)
    {
        $reflectionClass = new \ReflectionClass($name);
        $resourceAnnotation = $this->reader->getClassAnnotation($reflectionClass, Resource::class);

        if (null === $resourceAnnotation) {
            return;
        }

        $itemOperations = [];
        foreach ($resourceAnnotation->itemOperations as $itemOperationAnnotation) {
            $itemOperations[] = $this->getOperation($itemOperationAnnotation);
        }

        $collectionOperations = [];
        foreach ($resourceAnnotation->collectionOperations as $collectionOperationAnnotation) {
            $collectionOperations[] = $this->getOperation($collectionOperationAnnotation);
        }

        $shortName = null === $resourceAnnotation->shortName ? $this->shortNameGuesser($name) : $resourceAnnotation->shortName;
        $iri = $this->reader->getClassAnnotation($reflectionClass, Iri::class);

        return new Metadata($name, $shortName, $itemOperations, $collectionOperations, $iri);
    }

    /**
     * Converts an Operation annotation and its associated Pagination annotation to metadata.
     *
     * @param OperationAnnotation $operationAnnotation
     *
     * @return Operation
     */
    private function getOperation(OperationAnnotation $operationAnnotation)
    {
        $paginationAnnotation = $operationAnnotation->pagination;

        if (null === $paginationAnnotation) {
            $paginationSettings = null;
        } else {
            $paginationSettings = new PaginationSettings(
                $paginationAnnotation->enabled, $paginationAnnotation->itemsPerPage, $paginationAnnotation->clientControlEnabled
            );
        }

        return new Operation($operationAnnotation->attributes, $paginationSettings);
    }
}
