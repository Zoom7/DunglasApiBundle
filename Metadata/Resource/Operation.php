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
use Dunglas\ApiBundle\Annotation\Pagination;

/**
 * An operation doable on a resource.
 *
 * Can be an item or a collection operation.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class Operation
{
    /**
     * @var PaginationMetadata|null
     */
    private $paginationMetadata;

    /**
     * @var array|null
     */
    private $filters;

    /**
     * @var array
     */
    private $attributes;

    public function __construct(array $filters = null, PaginationMetadata $paginationMetadata = null, array $attributes = [])
    {
        $this->filters = $filters;
        $this->paginationMetadata = $paginationMetadata;
        $this->attributes = $attributes;
    }

    /**
     * Get filters.
     *
     * @return array|null
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Returns a new instance with the given filters.
     *
     * @param array $filters
     *
     * @return self
     */
    public function withFilters(array $filters) : self
    {
        $operation = clone $this;
        $operation->filters = $filters;

        return $operation;
    }

    /**
     * Gets pagination metadata
     *
     * @return PaginationMetadata|null
     */
    public function getPaginationMetadata()
    {
        return $this->paginationMetadata;
    }

    /**
     * Returns a new instance with the given pagination metadata.
     *
     * @param PaginationMetadata $paginationMetadata
     *
     * @return self
     */
    public function withPaginationMetadata(PaginationMetadata $paginationMetadata) : self
    {
        $metadata = clone $this;
        $metadata->paginationMetadata = $paginationMetadata;

        return $metadata;
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
     * Returns a new instance with given attributes.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return self
     */
    public function withAttribute(string $key, $value)
    {
        $operation = clone $this;
        $operation->filters[$key] = $value;

        return $operation;
    }
}
