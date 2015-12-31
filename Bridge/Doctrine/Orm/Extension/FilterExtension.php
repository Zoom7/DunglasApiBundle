<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Bridge\Doctrine\Orm\Extension;

use Doctrine\ORM\QueryBuilder;
use Dunglas\ApiBundle\Api\ResourceInterface;
use Dunglas\ApiBundle\Bridge\Doctrine\Orm\Filter\FilterInterface;
use Dunglas\ApiBundle\Bridge\Doctrine\Orm\QueryCollectionExtensionInterface;

/**
 * Applies filters on a resource query.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 * @author Samuel ROZE <samuel.roze@gmail.com>
 */
final class FilterExtension implements QueryCollectionExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function applyToCollection(string $resourceClass, QueryBuilder $queryBuilder)
    {
        foreach ($resource->getFilters() as $filter) {
            if ($filter instanceof FilterInterface) {
                $filter->apply($resource, $queryBuilder);
            }
        }
    }
}
