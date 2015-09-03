<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Mapping\Property\Loader\Annotation;

use Doctrine\Common\Annotations\Reader;
use Dunglas\ApiBundle\Annotation\Property;
use Dunglas\ApiBundle\Mapping\Property\Collection;
use Dunglas\ApiBundle\Mapping\Property\Loader\CollectionLoaderInterface;
use Dunglas\ApiBundle\Util\Reflection;

/**
 * Loads properties annotated with the {@see Property} annotation.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class ResourcePropertyCollectionLoader implements CollectionLoaderInterface
{
    /**
     * @var Reader
     */
    private $reader;
    /**
     * @var Reflection
     */
    private $reflection;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
        $this->reflection = new Reflection();
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection($resourceClass, array $options)
    {
        $properties = [];
        $reflectionClass = new \ReflectionClass($resourceClass);

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if ($this->reader->getPropertyAnnotation($reflectionProperty, Property::class)) {
                $properties[$reflectionProperty->name] = true;
            }
        }

        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            $propertyName = $this->reflection->getProperty($reflectionMethod->name);

            if ($propertyName && $this->reader->getMethodAnnotation($reflectionMethod, Property::class)) {
                $properties[$propertyName] = true;
            }
        }

        return 0 === count($properties) ? null : new Collection(array_keys($properties));
    }
}
