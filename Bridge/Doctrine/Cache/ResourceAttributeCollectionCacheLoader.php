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
use Dunglas\ApiBundle\Mapping\ResourceClassCollection\ResourceAttributeCollection;
use Dunglas\ApiBundle\Mapping\ResourceClassCollection\ResourcePropertyCollectionLoaderInterface;

/**
 * Cache decoractor.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class ResourcePropertyCollectionCacheLoader implements ResourcePropertyCollectionLoaderInterface
{
    const KEY_PATTERN = 'rac_%s_%s';

    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var ResourcePropertyCollectionLoaderInterface
     */
    private $loader;

    public function __construct(Cache $cache, ResourcePropertyCollectionLoaderInterface $loader)
    {
        $this->cache = $cache;
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceAttributeCollection($resourceClass, array $options)
    {
        $key = sprintf(self::KEY_PATTERN, $resourceClass, serialize($options));

        if ($this->cache->contains($key)) {
            return $this->cache->fetch($key);
        }

        $attributeCollection = $this->loader->getResourceAttributeCollection($resourceClass, $options);
        $this->cache->save($key, $attributeCollection);

        return $attributeCollection;
    }
}