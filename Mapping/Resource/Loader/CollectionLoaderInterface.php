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

use Dunglas\ApiBundle\Mapping\Resource\Collection;

/**
 * Loads a resource class collection.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
interface CollectionLoaderInterface
{
    /**
     * @return Collection
     */
    public function getCollection();
}
