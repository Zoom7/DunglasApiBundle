<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\JsonLd\EventListener;

use Dunglas\ApiBundle\JsonLd\Response as JsonLdResponse;
use Dunglas\ApiBundle\Metadata\Resource\Factory\ItemMetadataFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizes data then builds the response object.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class ResponderViewListener
{
    const FORMAT = 'jsonld';

    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @var ItemMetadataFactoryInterface
     */
    private $itemMetadataFactory;

    public function __construct(NormalizerInterface $normalizer, ItemMetadataFactoryInterface $itemMetadataFactory)
    {
        $this->normalizer = $normalizer;
        $this->itemMetadataFactory = $itemMetadataFactory;
    }

    /**
     * In an API context, converts any data to a JSON-LD response.
     *
     * @param GetResponseForControllerResultEvent $event
     *
     * @return JsonLdResponse|mixed
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $controllerResult = $event->getControllerResult();

        if ($controllerResult instanceof Response) {
            return;
        }

        $request = $event->getRequest();

        $format = $request->attributes->get('_api_format');
        if (self::FORMAT !== $format) {
            return;
        }

        switch ($request->getMethod()) {
            case Request::METHOD_POST:
                $status = 201;
                break;

            case Request::METHOD_DELETE:
                $status = 204;
                break;

            default:
                $status = 200;
                break;
        }

        $resourceClass = $request->attributes->get('_resource_class');
        $collectionOperationName = $request->attributes->get('_collection_operation_name');
        $itemOperationName = $request->attributes->get('_item_operation_name');

        if (!$resourceClass || (!$collectionOperationName && !$itemOperationName)) {
            $event->setResponse(new JsonLdResponse($controllerResult, $status));

            return;
        }

        $itemMetadata = $this->itemMetadataFactory->create($resourceClass);

        if ($collectionOperationName) {
            $context = $itemMetadata->getCollectionOperationAttribute($collectionOperationName, 'normalization_context');
        } else {
            $context = $itemMetadata->getItemOperationAttribute($itemOperationName, 'normalization_context');
        }

        if (!isset($context)) {
            $context = isset($itemMetadata->getAttributes()['normalization_context']) ? $itemMetadata->getAttributes()['normalization_context'] : [];
        }

        $response = new JsonLdResponse(
            $this->normalizer->normalize(
                $controllerResult, self::FORMAT, $context + ['request_uri' => $request->getRequestUri()]
            ),
            $status
        );

        $event->setResponse($response);
    }
}
