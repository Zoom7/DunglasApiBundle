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
use Dunglas\ApiBundle\Mapping\ResourceClassMetadata\ResourceClassMetadata;
use Dunglas\ApiBundle\Mapping\ResourceClassMetadata\ResourceClassMetadataLoaderInterface;

/**
 * Cache decorator.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class ResourceClassMetadataCacheLoader implements ResourceClassMetadataLoaderInterface
{
    const KEY_PATTERN = 'rcm_%s';

    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var ResourceClassMetadataLoaderInterface
     */
    private $loader;

    /**
     * @param Cache $cache
     * @param ResourceClassMetadataLoaderInterface $loader
     */
    public function __construct(Cache $cache, ResourceClassMetadataLoaderInterface $loader)
    {
        $this->cache = $cache;
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceClassMetadata(ResourceClassMetadata $resourceClassMetadata)
    {
        $key = sprintf(self::KEY_PATTERN, $resourceClassMetadata);

        if ($this->cache->contains($key)) {
            return $this->cache->fetch($key);
        }

        $resourceClassMetadata = $this->loader->getResourceClassMetadata($resourceClassMetadata);
        $this->cache->save($key, $resourceClassMetadata);

        return $resourceClassMetadata;
    }
}
