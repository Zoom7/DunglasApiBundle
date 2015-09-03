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

/**
 * Chain loader.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class MetadataLoaderChain implements MetadataLoaderInterface
{
    /**
     * @var MetadataLoaderInterface[]
     */
    private $loaders;

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
    public function getMetadata($resourceClass, $name, array $options)
    {
        foreach ($this->loaders as $loader) {
            $metadata = $loader->getMetadata($resourceClass, $name, $options);

            if (null !== $metadata) {
                return $metadata;
            }
        }
    }
}
