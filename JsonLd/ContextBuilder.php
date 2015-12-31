<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\JsonLd;

use Dunglas\ApiBundle\Api\ResourceTypeRegistryInterface;
use Dunglas\ApiBundle\Api\ResourceInterface;
use Dunglas\ApiBundle\JsonLd\Event\ContextBuilderEvent;
use Dunglas\ApiBundle\Metadata\Resource\Factory\ItemMetadataFactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * JSON-LD Context Builder.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class ContextBuilder
{
    const HYDRA_NS = 'http://www.w3.org/ns/hydra/core#';
    const RDF_NS = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
    const RDFS_NS = 'http://www.w3.org/2000/01/rdf-schema#';
    const XML_NS = 'http://www.w3.org/2001/XMLSchema#';
    const OWL_NS = 'http://www.w3.org/2002/07/owl#';

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    private $itemMetadataFactory;

    public function __construct(RouterInterface $router, EventDispatcherInterface $eventDispatcher, ItemMetadataFactoryInterface $itemMetadataFactory)
    {
        $this->router = $router;
        $this->eventDispatcher = $eventDispatcher;
        $this->itemMetadataFactory = $itemMetadataFactory;
    }

    /**
     * Builds the JSON-LD context for the entrypoint.
     *
     * @return array
     */
    public function getEntrypointContext() : array
    {
        $context = $this->getBaseContext();

        foreach ($this->resourceCollection as $resource) {
            $resourceName = lcfirst($resource->getShortName());

            $context[$resourceName] = [
                '@id' => 'Entrypoint/'.$resourceName,
                '@type' => '@id',
            ];
        }

        return $context;
    }

    /**
     * @param string $resourceClass
     * @param array  $normalizationContext
     *
     * @return array|string
     */
    public function getResourceContext(string $resourceClass, array $normalizationContext)
    {
        $itemMetadata = $this->itemMetadataFactory->create($resourceClass);
        // TODO

        if (isset($normalizationContext['jsonld_context_embedded'])) {
            return $this->getContext($resource);
        }

        return $this->getContextUri($resource);
    }

    /**
     * Builds the JSON-LD context for the given resource.
     *
     * @param string|null $resourceClass
     *
     * @return array
     */
    public function getContext(string $resourceClass = null) : array
    {
        $context = $this->getBaseContext();
        $event = new ContextBuilderEvent($context, $resourceClass);
        $this->eventDispatcher->dispatch(Event\Events::CONTEXT_BUILDER, $event);

        return $event->getContext();
    }

    /**
     * Gets the context URI for the given resource.
     *
     * @param ResourceInterface $resource
     *
     * @return string
     */
    public function getContextUri(string $resourceClass)
    {
        return $this->router->generate('api_jsonld_context', ['shortName' => $resource->getShortName()]);
    }

    /**
     * Gets the base context.
     *
     * @return array
     */
    private function getBaseContext()
    {
        return [
            '@vocab' => $this->router->generate('api_hydra_vocab', [], RouterInterface::ABSOLUTE_URL).'#',
            'hydra' => self::HYDRA_NS,
        ];
    }
}
