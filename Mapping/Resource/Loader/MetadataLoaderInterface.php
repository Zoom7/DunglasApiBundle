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

use Dunglas\ApiBundle\Mapping\Resource\Metadata;

/**
 * Loads a resource class metadata.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
interface MetadataLoaderInterface
{
    /**
     * Gets resource class metadata.
     *
     * @param string $name
     *
     * @return Metadata|null
     */
    public function getMetadata($name);
}
