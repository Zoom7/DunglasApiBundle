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
use Dunglas\ApiBundle\Mapping\Property\Loader\MetadataLoaderInterface;
use Dunglas\ApiBundle\Mapping\Property\Metadata;
use Dunglas\ApiBundle\Util\Reflection;

/**
 * Extracts data from {@see Property} annotation.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class MetadataLoader implements MetadataLoaderInterface
{
    /**
     * @var Reader
     */
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($resourceClass, $name, array $options)
    {
        $reflectionClass = new \ReflectionClass($resourceClass);

        if ($reflectionClass->hasProperty($name)) {
            $annotation = $this->reader->getPropertyAnnotation($reflectionClass->getProperty($name), Property::class);

            if (null !== $annotation) {
                return $this->getMetadataFromAnnotation($annotation);
            }
        }

        foreach (array_merge(Reflection::ACCESSOR_PREFIXES, Reflection::MUTATOR_PREFIXES) as $prefix) {
            $methodName = $prefix.ucfirst($name);

            if (!$reflectionClass->hasMethod($methodName)) {
                continue;
            }

            $reflectionMethod = $reflectionClass->getMethod($methodName);

            if (!$reflectionMethod->isPublic()) {
                continue;
            }

            $annotation = $this->reader->getMethodAnnotation($reflectionMethod, Property::class);
            if (null !== $annotation) {
                return $this->getMetadataFromAnnotation($annotation);
            }
        }
    }

    /**
     * Builds a metadata from an annotation.
     *
     * @param Property $annotation
     *
     * @return Metadata
     */
    private function getMetadataFromAnnotation(Property $annotation)
    {
        return new Metadata(
            null,
            $annotation->description,
            $annotation->readable,
            $annotation->writable,
            $annotation->readableLink,
            $annotation->writableLink,
            $annotation->required,
            $annotation->iri
        );
    }
}
