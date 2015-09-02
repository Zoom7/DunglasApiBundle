<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Bridge\Doctrine\Cache;

use Doctrine\Common\Cache\Cache;
use Dunglas\ApiBundle\Mapping\ResourceClassCollection\ResourceClassCollectionLoaderInterface;

/**
 * Cache decorator.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class ResourceClassCacheLoader implements ResourceClassCollectionLoaderInterface
{
    const KEY = 'resource_class_collection';

    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var ResourceClassCollectionLoaderInterface
     */
    private $loader;

    public function __construct(Cache $cache, ResourceClassCollectionLoaderInterface $loader)
    {
        $this->cache = $cache;
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceClassCollection()
    {
        if ($this->cache->contains(self::KEY)) {
            return $this->cache->fetch(self::KEY);
        }

        $resourceClassCollection = $this->loader->getResourceClassCollection();
        $this->cache->save(self::KEY, $resourceClassCollection);

        return $resourceClassCollection;
    }
}
