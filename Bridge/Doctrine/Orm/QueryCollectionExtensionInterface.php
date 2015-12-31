<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Bridge\Doctrine\Orm;

use Doctrine\ORM\QueryBuilder;

/**
 * Interface of Doctrine ORM query extensions for collection queries.
 *
 * @author Samuel ROZE <samuel.roze@gmail.com>
 */
interface QueryCollectionExtensionInterface
{
    /**
     * @param string       $resourceClass
     * @param QueryBuilder $queryBuilder
     */
    public function applyToCollection(string $resourceClass, QueryBuilder $queryBuilder);
}
