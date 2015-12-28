<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Bridge\Symfony\Routing;

use Doctrine\Common\Inflector\Inflector;
use Dunglas\ApiBundle\Api\ResourceTypeRegistryInterface;
use Dunglas\ApiBundle\Exception\RuntimeException;
use Dunglas\ApiBundle\Metadata\Property\Factory\ItemMetadataFactoryInterface;
use Dunglas\ApiBundle\Metadata\Resource\Factory\CollectionMetadataFactoryInterface;
use Dunglas\ApiBundle\Routing\ResourceCollectionInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Loads Resources.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class ApiLoader extends Loader
{
    const ROUTE_NAME_PREFIX = 'api_';
    const DEFAULT_ACTION_PATTERN = 'api.action.';

    /**
     * @var CollectionMetadataFactoryInterface
     */
    private $collectionMetadataFactory;

    /**
     * @var ItemMetadataFactoryInterface
     */
    private $itemMetadataFactory;

    /**
     * @var XmlFileLoader
     */
    private $fileLoader;

    public function __construct(KernelInterface $kernel, CollectionMetadataFactoryInterface $collectionMetadataFactory, ItemMetadataFactoryInterface $itemMetadataFactory)
    {
        $this->fileLoader = new XmlFileLoader(new FileLocator($kernel->locateResource('@DunglasApiBundle/Resources/config/routing')));
        $this->collectionMetadataFactory = $collectionMetadataFactory;
        $this->itemMetadataFactory = $itemMetadataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function load($data, $type = null)
    {
        $routeCollection = new RouteCollection();

        $routeCollection->addCollection($this->fileLoader->load('jsonld.xml'));
        $routeCollection->addCollection($this->fileLoader->load('hydra.xml'));

        foreach ($this->collectionMetadataFactory->create() as $resourceClass) {
            foreach ($this->itemMetadataFactory->create($resourceClass) as $itemMetadata) {
                $normalizedShortName = Inflector::pluralize(Inflector::tableize($itemMetadata->getShortName()));

                foreach ($itemMetadata->getCollectionOperations() as $operationName => $operation) {
                    $this->addRoute($routeCollection, $resourceClass, $operationName, $operation, $normalizedShortName, true);
                }

                foreach ($itemMetadata->getItemOperations() as $operationName => $operation) {
                    $this->addRoute($routeCollection, $resourceClass, $operationName, $operation, $normalizedShortName, false);
                }
            }
        }

        return $routeCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'api' === $type;
    }

    /**
     * Creates and adds a route for the given operation to the route collection.
     *
     * @param RouteCollection $routeCollection
     * @param string          $resourceClass
     * @param string          $operationName
     * @param array           $operation
     * @param string          $normalizedShortName
     * @param bool            $collection
     *
     * @throws RuntimeException
     */
    private function addRoute(RouteCollection $routeCollection, string $resourceClass, string $operationName, array $operation, string $normalizedShortName, bool $collection)
    {
        if (isset($collectionOperation['route_name'])) {
            return;
        }

        if (!isset($collectionOperation['method'])) {
            throw new RuntimeException('Either a "route_name" or a "method" key must exist.');
        }

        if (isset($collectionOperation['controller'])) {
            $actionName = sprintf('%s_%s', strtolower($collectionOperation['method']), $collection ? 'collection' : 'item');

            $controller = self::DEFAULT_ACTION_PATTERN.$actionName;
        } else {
            $controller = $collectionOperation['controller'];
        }

        $path = '/'.$normalizedShortName;
        if (!$collection) {
            $path .= '/{id}';
        }

        $routeName = sprintf('%s%s_%s', self::ROUTE_NAME_PREFIX, $normalizedShortName, $actionName);

        $route = new Route(
            $path,
            [
                '_controller' => $controller,
                '_resource_class' => $resourceClass,
                sprintf('_%s_operation', $collection ? 'collection' : 'item') => $operationName,
            ],
            [],
            [],
            '',
            [],
            [ $collectionOperation['method'] ]
        );

        $routeCollection->add($routeName, $route);
    }
}
