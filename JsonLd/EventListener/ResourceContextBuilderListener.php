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

use Dunglas\ApiBundle\JsonLd\Event\ContextBuilderEvent;
use Dunglas\ApiBundle\JsonLd\Event\Events;
use Dunglas\ApiBundle\Metadata\Resource\Factory\ItemMetadataFactoryInterface as ResourceItemMetadataFactoryInterface;
use Dunglas\ApiBundle\Metadata\Property\Factory\CollectionMetadataFactoryInterface as PropertyCollectionMetadataFactoryInterface;
use Dunglas\ApiBundle\Metadata\Property\Factory\ItemMetadataFactoryInterface as PropertyItemMetadataFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * Builds default context for JSON-LD resources.
 *
 * @author Luc Vieillescazes <luc@vieillescazes.net>
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class ResourceContextBuilderListener implements EventSubscriberInterface
{
    /**
     * @var ResourceItemMetadataFactoryInterface
     */
    private $resourceItemMetadataFactory;

    /**
     * @var PropertyCollectionMetadataFactoryInterface
     */
    private $propertyCollectionMetadataFactory;

    /**
     * @var PropertyItemMetadataFactoryInterface
     */
    private $propertyItemMetadataFactory;

    /**
     * @var NameConverterInterface
     */
    private $nameConverter;

    public function __construct(ResourceItemMetadataFactoryInterface $resourceItemMetadataFactory, PropertyCollectionMetadataFactoryInterface $propertyCollectionMetadataFactory, PropertyItemMetadataFactoryInterface $propertyItemMetadataFactoryInterface, NameConverterInterface $nameConverter = null)
    {
        $this->resourceItemMetadataFactory = $resourceItemMetadataFactory;
        $this->propertyCollectionMetadataFactory = $propertyCollectionMetadataFactory;
        $this->propertyItemMetadataFactory = $propertyItemMetadataFactoryInterface;
        $this->nameConverter = $nameConverter;
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::CONTEXT_BUILDER => ['onContextBuilder'],
        ];
    }

    /**
     * Builds default context.
     *
     * @param ContextBuilderEvent $event
     */
    public function onContextBuilder(ContextBuilderEvent $event)
    {
        $resourceClass = $event->getResourceClass();

        if (null === $resourceClass) {
            return;
        }

        $resourceItemMetadata = $this->resourceItemMetadataFactory->create($resourceClass);
        $context = $event->getContext();

        $prefixedShortName = sprintf('#%s', $resourceItemMetadata->getShortName());

        ;
        foreach ($this->propertyCollectionMetadataFactory->create($resourceClass) as $propertyName) {
            $propertyItemMetadata = $this->propertyItemMetadataFactory->create($resourceClass, $propertyName);

            if (!$propertyItemMetadata->isIdentifier()) {
                continue;
            }

            $convertedName = $this->nameConverter ? $this->nameConverter->normalize($propertyName) : $propertyName;

            if (!$id = $propertyItemMetadata->getIri()) {
                $id = sprintf('%s/%s', $prefixedShortName, $convertedName);
            }

            if ($propertyItemMetadata->isReadableLink()) {
                $context[$convertedName] = [
                    '@id' => $id,
                    '@type' => '@id',
                ];
            } else {
                $context[$convertedName] = $id;
            }
        }

        $event->setContext($context);
    }
}
