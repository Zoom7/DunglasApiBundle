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

use Dunglas\ApiBundle\Exception\InvalidArgumentException;
use Dunglas\ApiBundle\Metadata\Resource\Factory\CollectionMetadataFactoryInterface;
use Dunglas\ApiBundle\Util\ClassInfoTrait;

/**
 * {@inheritdoc}
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 * @author Samuel ROZE <samuel.roze@gmail.com>
 */
final class ResourceClassResolver implements ResourceClassResolverInterface
{
    use ClassInfoTrait;

    /**
     * @var CollectionMetadataFactoryInterface
     */
    private $collectionMetadataFactory;

    public function __construct(CollectionMetadataFactoryInterface $collectionMetadataFactory)
    {
        $this->collectionMetadataFactory = $collectionMetadataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceClass($value, string $resourceClass = null, bool $strict = false) : string
    {
        if (is_object($value) && !$value instanceof PaginatorInterface) {
            $typeToFind = $type = $this->getObjectClass($value);
        } elseif (null === $resourceClass) {
            throw new InvalidArgumentException(sprintf('No resource class found.'));
        } else {
            $typeToFind = $resourceClass;
        }

        if (!$this->isResourceClass($typeToFind) || ($strict && isset($type) && $resourceClass !== $type)) {
            if (is_subclass_of($type, $resourceClass) && $this->isResourceClass($type)) {
                return $type;
            }

            throw new InvalidArgumentException(sprintf('No resource class found for object of type "%s"', $typeToFind));
        }

        return $resourceClass;
    }

    /**
     * {@inheritdoc}
     */
    public function isResourceClass(string $type) : bool
    {
        foreach ($this->collectionMetadataFactory->create() as $resourceClass) {
            if ($type === $resourceClass) {
                return true;
            }
        }

        return false;
    }
}
