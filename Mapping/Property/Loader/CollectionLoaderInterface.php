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

use Dunglas\ApiBundle\Mapping\Property\Collection;

/**
 * Properties collection loader interface.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
interface CollectionLoaderInterface
{
    /**
     * Gets resource properties for the given options.
     *
     * @param string $resourceClass
     * @param array  $options
     *
     * @return Collection|null
     */
    public function getCollection($resourceClass, array $options);
}
