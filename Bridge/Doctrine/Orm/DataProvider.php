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

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Dunglas\ApiBundle\Exception\InvalidArgumentException;
use Dunglas\ApiBundle\Metadata\Property\Factory\CollectionMetadataFactoryInterface;
use Dunglas\ApiBundle\Metadata\Property\Factory\ItemMetadataFactoryInterface;
use Dunglas\ApiBundle\Api\DataProviderInterface;

/**
 * Data provider for the Doctrine ORM.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 * @author Samuel ROZE <samuel.roze@gmail.com>
 */
final class DataProvider implements DataProviderInterface
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var CollectionMetadataFactoryInterface
     */
    private $collectionMetadataFactory;

    /**
     * @var ItemMetadataFactoryInterface
     */
    private $itemMetadataFactory;

    /**
     * @var DataProviderInterface|null
     */
    private $decorated;

    /**
     * @var QueryItemExtensionInterface[]
     */
    private $itemExtensions;

    /**
     * @var QueryCollectionExtensionInterface[]
     */
    private $collectionExtensions;

    /**
     * @param ManagerRegistry                     $managerRegistry
     * @param CollectionMetadataFactoryInterface  $collectionMetadataFactory
     * @param ItemMetadataFactoryInterface        $itemMetadataFactory
     * @param QueryCollectionExtensionInterface[] $collectionExtensions
     * @param QueryItemExtensionInterface[]       $itemExtensions
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        CollectionMetadataFactoryInterface $collectionMetadataFactory,
        ItemMetadataFactoryInterface $itemMetadataFactory,
        DataProviderInterface $decorated = null,
        array $collectionExtensions = [],
        array $itemExtensions = []
    ) {
        $this->managerRegistry = $managerRegistry;
        $this->collectionMetadataFactory = $collectionMetadataFactory;
        $this->itemMetadataFactory = $itemMetadataFactory;
        $this->decorated = $decorated;
        $this->itemExtensions = $itemExtensions;
        $this->collectionExtensions = $collectionExtensions;
    }

    /**
     * {@inheritdoc}
     */
    public function getItem(string $resourceClass, $id, bool $fetchData = false)
    {
        if ($this->decorated && $item = $this->decorated->getItem($resourceClass, $id, $fetchData)) {
            return $item;
        }

        $manager = $this->managerRegistry->getManagerForClass($resourceClass);

        $identifierValues = explode('-', $id);
        $identifiers = [];
        $i = 0;

        foreach ($this->collectionMetadataFactory->create($resourceClass) as $propertyName) {
            $itemMetadata = $this->itemMetadataFactory->create($resourceClass, $propertyName);

            if (!$itemMetadata->isIdentifier()) {
                continue;
            }

            if (!isset($identifierValues[$i])) {
                throw new InvalidArgumentException(sprintf('Invalid identifier "%s".', $id));
            }

            $identifiers[$propertyName] = $identifierValues[$i];
            ++$i;
        }

        if (!$fetchData || $manager instanceof EntityManagerInterface) {
            return $manager->getReference($resourceClass, $identifiers);
        }

        $repository = $manager->getRepository($resourceClass);
        $queryBuilder = $repository->createQueryBuilder('o');

        foreach ($identifiers as $propertyName => $value) {
            $placeholder = 'id_'.$propertyName;

            $queryBuilder
                ->where($queryBuilder->expr()->eq('o.' . $propertyName, ':'.$placeholder))
                ->setParameter($placeholder, $value)
            ;
        }

        foreach ($this->itemExtensions as $extension) {
            $extension->applyToItem($resourceClass, $queryBuilder, $identifiers);
        }

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection(string $resourceClass)
    {
        $manager = $this->managerRegistry->getManagerForClass($resourceClass);
        $repository = $manager->getRepository($resourceClass);
        $queryBuilder = $repository->createQueryBuilder('o');

        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($resourceClass, $queryBuilder);

            if ($extension instanceof QueryResultExtensionInterface) {
                if ($extension->supportsResult($resourceClass)) {
                    return $extension->getResult($queryBuilder);
                }
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $resourceClass) : bool
    {
        return null !== $this->managerRegistry->getManagerForClass($resourceClass);
    }
}
