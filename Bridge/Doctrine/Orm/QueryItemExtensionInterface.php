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
 * Interface of Doctrine ORM query extensions for item queries.
 *
 * @author Maxime STEINHAUSSER <maxime.steinhausser@gmail.com>
 */
interface QueryItemExtensionInterface
{
    /**
     * @param string       $resourceClass
     * @param QueryBuilder $queryBuilder
     * @param array        $id
     */
    public function applyToItem(string $resourceClass, QueryBuilder $queryBuilder, array $identifiers);
}
