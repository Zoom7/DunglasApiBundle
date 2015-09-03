<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Bridge\Doctrine\Cache\Mapping\Property;

use Doctrine\Common\Cache\Cache;
use Dunglas\ApiBundle\Mapping\Property\Loader\CollectionLoaderInterface;
use Dunglas\ApiBundle\Mapping\ResourceClassCollection\ResourceAttributeCollection;
use Dunglas\ApiBundle\Mapping\ResourceClassCollection\ResourcePropertyCollectionLoaderInterface;

/**
 * Cache decorator.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class CollectionLoaderDecoractor implements CollectionLoaderInterface
{
    const KEY_PATTERN = 'pc_%s_%s';

    /**
     * @var CollectionLoaderInterface
     */
    private $loader;
    /**
     * @var Cache
     */
    private $cache;

    public function __construct(CollectionLoaderInterface $loader, Cache $cache)
    {
        $this->loader = $loader;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection($resourceClass, array $options)
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
