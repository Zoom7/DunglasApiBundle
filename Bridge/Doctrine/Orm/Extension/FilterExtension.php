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
use Dunglas\ApiBundle\Doctrine\Orm\Filter\FilterInterface;
use Dunglas\ApiBundle\Metadata\Resource\Factory\ItemMetadataFactoryInterface;

/**
 * Applies filters on a resource query.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 * @author Samuel ROZE <samuel.roze@gmail.com>
 */
final class FilterExtension implements QueryCollectionExtensionInterface
{
    /**
     * @var ItemMetadataFactoryInterface
     */
    private $itemMetadataFactory;

    /**
     * @var FilterInterface[]
     */
    private $filters;

    public function __construct(array $filters, ItemMetadataFactoryInterface $itemMetadataFactory)
    {
        $this->filters = $filters;
        $this->itemMetadataFactory = $itemMetadataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function applyToCollection(QueryBuilder $queryBuilder, string $resourceClass, string $operationName = null)
    {
        $itemMetadata = $this->itemMetadataFactory->create($resourceClass);
        $filterClasses = $itemMetadata->getCollectionOperationAttribute($operationName, 'filters', [], true);

        foreach ($this->filters as $filter) {
            if ($filter instanceof FilterInterface && in_array(get_class($filter), $filterClasses)) {
                $filter->apply($queryBuilder, $resourceClass, $operationName);
            }
        }
    }
}
