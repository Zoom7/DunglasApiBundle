<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Bridge\Doctrine\Cache\Property;

use Doctrine\Common\Cache\Cache;
use Dunglas\ApiBundle\Mapping\Property\Loader\Collection\LoaderInterface;

/**
 * Cache decorator.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class LoaderDecorator implements LoaderInterface
{
    const KEY_PATTERN = 'pc_%s_%s';

    /**
     * @var LoaderInterface
     */
    private $loader;
    /**
     * @var Cache
     */
    private $cache;

    public function __construct(LoaderInterface $loader, Cache $cache)
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

        $attributeCollection = $this->loader->getCollection($resourceClass, $options);
        $this->cache->save($key, $attributeCollection);

        return $attributeCollection;
    }
}
