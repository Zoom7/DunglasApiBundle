<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Mapping\Resource\Collection;

/**
 * A collection of resource classes.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class Collection implements \IteratorAggregate, \Countable
{
    /**
     * @var string[]
     */
    private $classes;

    /**
     * @param string[] $classes
     */
    public function __construct(array $classes = [])
    {
        $this->classes = $classes;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->classes);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->classes);
    }

    /**
     * Creates a new instance containing classes from the current instance and the instance passed in argument.
     *
     * @param self $resourceClassCollection
     *
     * @return self
     */
    public function merge(Collection $resourceClassCollection) {
        return new self(array_merge($this->classes, $resourceClassCollection->classes));
    }
}
