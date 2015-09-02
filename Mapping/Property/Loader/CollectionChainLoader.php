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

use Dunglas\ApiBundle\Mapping\Property\Collection;
use Dunglas\ApiBundle\Mapping\Property\ResourcePropertyCollectionLoaderInterface;

/**
 * Retrieves the list of attributes for a given resource class and a set of options.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class ResourcePropertyCollectionChainLoader implements CollectionLoaderInterface
{
    /**
     * @var CollectionLoaderInterface[]
     */
    private $loaders;

    /**
     * @param CollectionLoaderInterface[] $loaders
     */
    public function __construct(array $loaders)
    {
        $this->loaders = $loaders;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection($resourceClass, array $options)
    {
        $collection = new Collection();

        foreach ($this->loaders as $loader) {
            $collection = $collection->merge($loader->getCollection($resourceClass, $options));
        }

        return $collection;
    }
}
