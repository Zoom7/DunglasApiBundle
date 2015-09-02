<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Mapping\Property;

/**
 * A collection of properties for a given resource.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class ResourcePropertyCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var string[]
     */
    private $properties;

    /**
     * @param string[] $properties
     */
    public function __construct(array $properties = [])
    {
        $this->properties = $properties;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->properties);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->properties);
    }

    /**
     * Creates a new instance containing classes from the current instance and the instance passed in argument.
     *
     * @param self $collection
     *
     * @return self
     */
    public function merge(Collection $collection) {
        return new self(array_merge($this->properties, $collection->properties));
    }
}
