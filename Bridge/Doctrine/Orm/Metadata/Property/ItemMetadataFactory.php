<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Bridge\Doctrine\Orm\Metadata\Property;

use Doctrine\Common\Persistence\ManagerRegistry;
use Dunglas\ApiBundle\Metadata\Property\Factory\ItemMetadataFactoryInterface;
use Dunglas\ApiBundle\Metadata\Property\ItemMetadata;

/**
 * Use Doctrine metadata to populate the identifier property.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class ItemMetadataFactory implements ItemMetadataFactoryInterface
{
    /**
     * @var ItemMetadataFactoryInterface
     */
    private $decorated;

    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry, ItemMetadataFactoryInterface $decorated)
    {
        $this->managerRegistry = $managerRegistry;
        $this->decorated = $decorated;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $resourceClass, string $property, array $options) : ItemMetadata
    {
        $itemMetadata = $this->decorated->create($resourceClass, $property, $options);

        if (null !== $itemMetadata->getIdentifier()) {
            return $itemMetadata;
        }

        $manager = $this->managerRegistry->getManagerForClass($resourceClass);
        if (!$manager) {
            return $itemMetadata;
        }

        $doctrineClassMetadata = $manager->getClassMetadata($resourceClass);
        if (!$doctrineClassMetadata) {
            return $itemMetadata;
        }

        $identifiers = $doctrineClassMetadata->getIdentifier();

        foreach ($identifiers as $identifier) {
            if ($identifier === $property) {
                $itemMetadata = $itemMetadata->withIdentifier(true);

                if ($doctrineClassMetadata->isIdentifierNatural()) {
                    $itemMetadata = $itemMetadata->withWritable(false);
                }

                break;
            }
        }

        if (null === $itemMetadata->getIdentifier()) {
            $itemMetadata = $itemMetadata->withIdentifier(false);
        }

        return $itemMetadata;
    }
}
