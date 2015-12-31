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
 * Interface of Doctrine ORM query extensions that supports result production
 * for specific cases such as pagination.
 *
 * @author Samuel ROZE <samuel.roze@gmail.com>
 */
interface QueryResultExtensionInterface extends QueryCollectionExtensionInterface
{
    /**
     * @param string $resourceClass
     *
     * @return bool
     */
    public function supportsResult(string $resourceClass) : bool;

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return mixed
     */
    public function getResult(QueryBuilder $queryBuilder);
}
