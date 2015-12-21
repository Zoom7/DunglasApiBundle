<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Bridge\Doctrine\Cache\Mapping\Resource;

use Doctrine\Common\Cache\Cache;
use Dunglas\ApiBundle\Mapping\Resource\Loader\Metadata\LoaderInterface;

/**
 * Cache decorator.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class LoaderDecorator implements LoaderInterface
{
    const KEY_PATTERN = 'r_%s';

    /**
     * @var \Dunglas\ApiBundle\Mapping\Resource\Loader\Metadata\LoaderInterface
     */
    private $loader;
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @param Cache $cache
     * @param \Dunglas\ApiBundle\Mapping\Resource\Loader\Metadata\LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader, Cache $cache)
    {
        $this->loader = $loader;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($name)
    {
        $key = sprintf(self::KEY_PATTERN, $name);

        if ($this->cache->contains($key)) {
            return $this->cache->fetch($key);
        }

        $metadata = $this->loader->getMetadata($name);
        $this->cache->save($key, $metadata);

        return $metadata;
    }
}
