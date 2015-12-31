<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Action;

use Dunglas\ApiBundle\Exception\RuntimeException;
use Dunglas\ApiBundle\Metadata\Resource\Factory\ItemMetadataFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Add a new resource to a collection.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class PostCollectionAction
{
    use ActionUtilTrait;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ItemMetadataFactoryInterface
     */
    private $itemMetadataFactory;

    public function __construct(SerializerInterface $serializer, ItemMetadataFactoryInterface $itemMetadataFactory)
    {
        $this->serializer = $serializer;
        $this->itemMetadataFactory = $itemMetadataFactory;
    }

    /**
     * Hydrate an item to persist.
     *
     * @param Request $request
     *
     * @return mixed
     *
     * @throws RuntimeException
     */
    public function __invoke(Request $request)
    {
        list($resourceClass, $operationName, , $format) = $this->extractAttributes($request);
        $itemMetadata = $this->itemMetadataFactory->create($resourceClass);

        return $this->serializer->deserialize(
            $request->getContent(),
            $resourceClass,
            $format,
            $itemMetadata->getCollectionOperationAttribute($operationName, 'denormalization_context', [], true)
        );
    }
}
