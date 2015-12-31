<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Api;

use Dunglas\ApiBundle\Api\ResourceInterface;
use Dunglas\ApiBundle\Model\bool;
use Dunglas\ApiBundle\Model\PaginatorInterface;
use Dunglas\ApiBundle\Model\string;

/**
 * Data provider interface.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
interface DataProviderInterface
{
    /**
     * Retrieves an item.
     *
     * @param string      $resourceClass
     * @param string|null $operationName
     * @param int|string  $id
     * @param bool        $fetchData
     *
     * @return object|null
     */
    public function getItem(string $resourceClass, $id, string $operationName = null, bool $fetchData = false);

    /**
     * Retrieves a collection.
     *
     * @param string      $resourceClass
     * @param string|null $operationName
     *
     * @return array|PaginatorInterface|\Traversable
     */
    public function getCollection(string $resourceClass, string $operation = null);
}
