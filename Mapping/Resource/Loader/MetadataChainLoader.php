<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Mapping\Resource\Loader;

/**
 * Chain loader.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class MetadataChainLoader implements MetadataLoaderInterface
{
    /**
     * @param MetadataLoaderInterface[] $loaders
     */
    public function __construct(array $loaders)
    {
        $this->loaders = $loaders;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($name)
    {
        foreach ($this->loaders as $loader) {
            $metadata = $loader->getMetadata($name);

            if (null !== $metadata) {
                return $metadata;
            }
        }
    }
}
