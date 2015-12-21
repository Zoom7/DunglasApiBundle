<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Mapping\Resource\Loader\Collection;

use Dunglas\ApiBundle\Mapping\Resource\Collection;

/**
 * Chained loader.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class ChainLoader implements LoaderInterface
{
    /**
     * @var LoaderInterface[]
     */
    private $loaders;

    /**
     * @param LoaderInterface[] $loaders
     */
    public function __construct(array $loaders)
    {
        $this->loaders = $loaders;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        $resourceClassCollection = new Collection();

        foreach ($this->loaders as $loader) {
            $resourceClassCollection = $resourceClassCollection->merge($loader->getCollection());
        }

        return $resourceClassCollection;
    }
}
