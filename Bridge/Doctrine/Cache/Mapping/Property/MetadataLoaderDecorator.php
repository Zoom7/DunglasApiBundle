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
use Dunglas\ApiBundle\Mapping\Property\Loader\MetadataLoaderInterface;

/**
 * Property metadata loader cache decorator.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class MetadataLoaderDecorator implements MetadataLoaderInterface
{
    const KEY_PATTERN = 'p_%s_%s_%s';

    /**
     * @var MetadataLoaderInterface
     */
    private $loader;
    /**
     * @var Cache
     */
    private $cache;

    public function __construct(MetadataLoaderInterface $loader, Cache $cache)
    {
        $this->loader = $loader;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($resourceClass, $name, array $options)
    {
        $key = sprintf(self::KEY_PATTERN, $name, serialize($options));

        if ($this->cache->contains($key)) {
            return $this->cache->fetch($key);
        }

        $metadata = $this->loader->getMetadata($resourceClass, $name, $options);
        $this->cache->save($key, $metadata);

        return $metadata;
    }
}
