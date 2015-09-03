<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Mapping\Property\Loader\Reflection;

use Dunglas\ApiBundle\Mapping\Property\Collection;
use Dunglas\ApiBundle\Mapping\Property\Loader\CollectionLoaderInterface;
use Dunglas\ApiBundle\Util\Reflection;

/**
 * Finds properties using mutator methods.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class CollectionLoader implements CollectionLoaderInterface
{
    /**
     * @var Reflection
     */
    private $reflection;

    public function __construct()
    {
        $this->reflection = new Reflection();
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection($resourceClass, array $options)
    {
        $reflectionClass = new \ReflectionClass($resourceClass);

        $properties = [];

        foreach ($reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $properties[$reflectionProperty->name] = true;
        }

        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            $propertyName = $this->reflection->getProperty($reflectionMethod->name);

            if ($propertyName) {
                $properties[$propertyName] = true;
            }
        }

        return new Collection(array_keys($properties));
    }
}
