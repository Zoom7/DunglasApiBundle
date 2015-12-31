<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\EventListener;

use Dunglas\ApiBundle\Exception\ValidationException;
use Dunglas\ApiBundle\Metadata\Resource\Factory\ItemMetadataFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Validates data.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class ValidationViewListener
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ItemMetadataFactoryInterface
     */
    private $itemMetadataFactory;

    public function __construct(ValidatorInterface $validator, ItemMetadataFactoryInterface $itemMetadataFactory)
    {
        $this->validator = $validator;
        $this->itemMetadataFactory = $itemMetadataFactory;
    }

    /**
     * Validates data returned by the controller if applicable.
     *
     * @param GetResponseForControllerResultEvent $event
     *
     * @return mixed
     *
     * @throws ValidationException
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();

        $resourceClass = $request->attributes->get('_resource_class');
        $itemOperationName = $request->attributes->get('_item_operation_name');
        $collectionOperationName = $request->attributes->get('_collection_operation_name');

        if (!$resourceClass || (!$itemOperationName && !$collectionOperationName) || !in_array($request->getMethod(), [Request::METHOD_POST, Request::METHOD_PUT])) {
            return;
        }

        $data = $event->getControllerResult();

        $itemMetadata = $this->itemMetadataFactory->create($resourceClass);

        if ($collectionOperationName) {
            $validationGroups = $itemMetadata->getCollectionOperationAttribute($collectionOperationName, 'validation_groups');
        } else {
            $validationGroups = $itemMetadata->getItemOperationAttribute($itemOperationName, 'validation_groups');
        }

        if (!$validationGroups) {
            // Fallback to the resource
            $validationGroups = isset($itemMetadata->getAttributes()['validation_groups']) ? $itemMetadata->getAttributes()['validation_groups'] : null;
        }

        if (is_callable($validationGroups)) {
            $validationGroups = call_user_func_array($validationGroups, [ $data ]);
        }

        $violations = $this->validator->validate($data, null, $validationGroups);
        if (0 !== count($violations)) {
            throw new ValidationException($violations);
        }
    }
}
