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
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Dunglas\ApiBundle\Api\ResourceInterface;
use Dunglas\ApiBundle\Bridge\Doctrine\Orm\QueryCollectionExtensionInterface;
use Dunglas\ApiBundle\Bridge\Doctrine\Orm\QueryItemExtensionInterface;

/**
 * Eager loads relations.
 *
 * @author Charles Sarrazin <charles@sarraz.in>
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class EagerLoadingExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function applyToCollection(string $resourceClass, QueryBuilder $queryBuilder)
    {
        $this->joinRelations($resourceClass, $queryBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function applyToItem(string $resourceClass, QueryBuilder $queryBuilder, array $id)
    {
        $this->joinRelations($resourceClass, $queryBuilder);
    }

    /**
     * Left joins relations to eager load.
     *
     * @param string       $resourceClass
     * @param QueryBuilder $queryBuilder
     */
    private function joinRelations(string $resourceClass, QueryBuilder $queryBuilder)
    {
        $classMetaData = $queryBuilder->getEntityManager()->getClassMetadata($resourceClass);

        foreach ($classMetaData->getAssociationNames() as $i => $association) {
            $mapping = $classMetaData->associationMappings[$association];
            if (ClassMetadataInfo::FETCH_EAGER === $mapping['fetch']) {
                $queryBuilder->leftJoin('o.'.$association, 'a'.$i);
                $queryBuilder->addSelect('a'.$i);
            }
        }
    }
}
