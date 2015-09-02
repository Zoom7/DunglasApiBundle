<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Mapping\Resource;

/**
 * Resource class metadata.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class Metadata
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string|null
     */
    private $shortName;
    /**
     * @var Operation[]
     */
    private $collectionOperations;
    /**
     * @var Operation[]
     */
    private $itemOperations;
    /**
     * @var string|null
     */
    private $iri;
    /**
     * @var string|null
     */
    private $description;

    /**
     * @param string      $name
     * @param string|null $shortName
     * @param Operation[] $collectionOperations
     * @param Operation[] $itemOperations
     * @param string|null $iri
     * @param string      $description
     */
    public function __construct($name, $shortName, array $collectionOperations, array $itemOperations, $iri, $description)
    {
        $this->name = $name;
        $this->shortName = $shortName;
        $this->collectionOperations = $collectionOperations;
        $this->itemOperations = $itemOperations;
        $this->iri = $iri;
        $this->description = $description;
    }

    /**
     * Gets the class name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the short name.
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Returns a new instance of Metadata with the given short name.
     *
     * @param string $name
     *
     * @return self
     */
    public function withShortName($name)
    {
        $metadata = clone $this;
        $metadata->name = $name;

        return $metadata;
    }

    /**
     * Gets collection operations.
     *
     * @return Operation[]
     */
    public function getCollectionOperations()
    {
        return $this->collectionOperations;
    }

    /**
     * Returns a new instance of Metadata with the given item operations.
     *
     * @param Operation[] $collectionOperations
     *
     * @return self
     */
    public function withCollectionOperations(array $collectionOperations)
    {
        $metadata = clone $this;
        $metadata->collectionOperations = $collectionOperations;

        return $metadata;
    }

    /**
     * Gets item operations.
     *
     * @return Operation[]
     */
    public function getItemOperations()
    {
        return $this->itemOperations;
    }

    /**
     * Returns a new instance of Metadata with the given item operations.
     *
     * @param Operation[] $itemOperations
     *
     * @return self
     */
    public function withItemOperations(array $itemOperations)
    {
        $metadata = clone $this;
        $metadata->itemOperations = $itemOperations;

        return $metadata;
    }

    /**
     * Gets the associated IRI.
     */
    public function getIri()
    {
        return $this->iri;
    }

    /**
     * Returns a new instance of Metadata with the given IRI.
     *
     * @param string $iri
     *
     * @return self
     */
    public function withIri($iri)
    {
        $metadata = clone $this;
        $metadata->iri = $iri;

        return $metadata;
    }

    /**
     * Gets the description.
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Returns a new instance of Metadata with the given description.
     *
     * @param string $description
     *
     * @return self
     */
    public function withDescription($description)
    {
        $metadata = clone $this;
        $metadata->description = $description;

        return $metadata;
    }
}
