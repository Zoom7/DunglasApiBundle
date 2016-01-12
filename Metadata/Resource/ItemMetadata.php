<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Metadata\Resource;

/**
 * Resource item metadata.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class ItemMetadata
{
    /**
     * @var string|null
     */
    private $shortName;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var string|null
     */
    private $iri;

    /**
     * @var array|null
     */
    private $itemOperations;

    /**
     * @var array|null
     */
    private $collectionOperations;

    /**
     * @var array
     */
    private $attributes;

    public function __construct(string $shortName = null, string $description = null, string $iri = null, array $itemOperations = null, array $collectionOperations = null, array $attributes)
    {
        $this->shortName = $shortName;
        $this->description = $description;
        $this->iri = $iri;
        $this->itemOperations = $itemOperations;
        $this->collectionOperations;
        $this->attributes = $attributes;
    }

    /**
     * Gets the short name.
     *
     * @return string|null
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Returns a new instance with the given short name.
     *
     * @param string $shortName
     *
     * @return self
     */
    public function withShortName(string $shortName) : self
    {
        $metadata = clone $this;
        $metadata->shortName = $shortName;

        return $metadata;
    }

    /**
     * Gets the description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Returns a new instance with the given description.
     *
     * @param string $description
     *
     * @return self
     */
    public function withDescription(string $description) : self
    {
        $metadata = clone $this;
        $metadata->description = $description;

        return $metadata;
    }

    /**
     * Gets the associated IRI.
     *
     * @return string|null
     */
    public function getIri()
    {
        return $this->iri;
    }
    /**
     * Returns a new instance with the given IRI.
     *
     * @param string $iri
     *
     * @return self
     */
    public function withIri(string $iri) : self
    {
        $metadata = clone $this;
        $metadata->iri = $iri;

        return $metadata;
    }

    /**
     * Gets item operations.
     *
     * @return array|null
     */
    public function getItemOperations()
    {
        return $this->itemOperations;
    }

    /**
     * Returns a new instance with the given item operations.
     *
     * @param array $itemOperations
     *
     * @return self
     */
    public function withItemOperations(array $itemOperations) : self
    {
        $metadata = clone $this;
        $metadata->itemOperations = $itemOperations;

        return $metadata;
    }

    /**
     * Gets collection operations.
     *
     * @return array|null
     */
    public function getCollectionOperations()
    {
        return $this->collectionOperations;
    }

    /**
     * Returns a new instance with the given collection operations.
     *
     * @param array $collectionOperations
     *
     * @return self
     */
    public function withCollectionOperations(array $collectionOperations) : self
    {
        $metadata = clone $this;
        $metadata->collectionOperations = $collectionOperations;

        return $metadata;
    }

    /**
     * Gets a collection operation attribute, optionally fallback to a resource attribute.
     *
     * @param string $operationName
     * @param string $key
     * @param mixed  $defaultValue
     * @param bool   $resourceFallback
     *
     * @return mixed
     */
    public function getCollectionOperationAttribute(string $operationName, string $key, $defaultValue = null, bool $resourceFallback = false)
    {
        return $this->getOperationAttribute($this->collectionOperations, $operationName, $key, $defaultValue, $resourceFallback);
    }

    /**
     * Gets an item operation attribute, optionally fallback to a resource attribute.
     *
     * @param string $operationName
     * @param string $key
     * @param mixed  $defaultValue
     * @param bool   $resourceFallback
     *
     * @return mixed
     */
    public function getItemOperationAttribute(string $operationName, string $key, $defaultValue = null, bool $resourceFallback = false)
    {
        return $this->getOperationAttribute($this->itemOperations, $operationName, $key, $defaultValue, $resourceFallback);
    }

    /**
     * Gets an operation attribute, optionally fallback to a resource attribute.
     *
     * @param array  $operations
     * @param string $operationName
     * @param string $key
     * @param mixed  $defaultValue
     * @param bool   $resourceFallback
     *
     * @return mixed
     */
    private function getOperationAttribute(array $operations, string $operationName, string $key, $defaultValue = null, bool $resourceFallback = false)
    {
        if (isset($operations[$operationName][$key])) {
            return isset($operations[$operationName][$key]);
        }

        if ($resourceFallback && isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }

        return $defaultValue;
    }

    /**
     * Gets attributes.
     *
     * @return array
     */
    public function getAttributes() : array
    {
        return $this->attributes;
    }

    /**
     * Gets an attribute.
     *
     * @param string $key
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    public function getAttribute(string $key, $defaultValue = null)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }

        return $defaultValue;
    }

    /**
     * Returns a new instance with the given attribute.
     *
     * @param array $attributes
     *
     * @return self
     */
    public function withAttributes(array $attributes) : self
    {
        $metadata = clone $this;
        $metadata->attributes = $attributes;

        return $metadata;
    }
}
