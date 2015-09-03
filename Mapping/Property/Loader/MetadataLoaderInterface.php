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

use Dunglas\ApiBundle\Mapping\Property\Metadata;

/**
 * Property metadata loader interface.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
interface MetadataLoaderInterface
{
    /**
     * Gets a property metadata.
     *
     * @param string $resourceClass
     * @param string $name
     * @param array $options
     *
     * @return Metadata|null
     */
    public function getMetadata($resourceClass, $name, array $options);
}
