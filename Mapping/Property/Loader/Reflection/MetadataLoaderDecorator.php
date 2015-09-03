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

use Dunglas\ApiBundle\Mapping\Property\Loader\MetadataLoaderInterface;
use Dunglas\ApiBundle\Mapping\Property\Metadata;
use Dunglas\ApiBundle\Util\Reflection;

/**
 * Decorates the loader using reflection to fill the metadata.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class MetadataLoaderDecorator implements MetadataLoaderInterface
{
    /**
     * @var MetadataLoaderInterface
     */
    private $loader;

    public function __construct(MetadataLoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($resourceClass, $name, array $options)
    {
        $metadata = $this->loader->getMetadata($resourceClass, $name, $options);
        if (null !== $metadata && null !== $metadata->isReadable() && null !== $metadata->isWritable()) {
            return $metadata;
        }

        $reflectionClass = new \ReflectionClass($resourceClass);
        if ($reflectionClass->hasProperty($name)) {
            return $this->getUpdatedMetadata($metadata, true, true);
        }

        $ucFirst = ucfirst($name);
        foreach (Reflection::ACCESSOR_PREFIXES as $prefix) {
            $methodName = $prefix.$ucFirst;
            if (!$reflectionClass->hasMethod($methodName)) {
                continue;
            }

            $reflectionMethod = $reflectionClass->getMethod($methodName);
            if (!$reflectionMethod->isPublic() || $reflectionMethod->getNumberOfRequiredParameters() > 0) {
                continue;
            }

            $readable = true;
        }

        foreach (Reflection::MUTATOR_PREFIXES as $prefix) {
            $methodName = $prefix.$ucFirst;
            if (!$reflectionClass->hasMethod($methodName)) {
                continue;
            }

            $reflectionMethod = $reflectionClass->getMethod($methodName);
            if (!$reflectionMethod->isPublic() || $reflectionMethod->getNumberOfRequiredParameters() > 1) {
                continue;
            }

            $writable = true;
        }

        return $this->getUpdatedMetadata($metadata, $readable, $writable);
    }

    /**
     * Creates a new metadata instance with updated readable and writable flags.
     *
     * @param Metadata|null $metadata
     * @param bool          $readable
     * @param bool          $writable
     *
     * @return Metadata
     */
    private function getUpdatedMetadata(Metadata $metadata = null, $readable, $writable)
    {
        if (null === $metadata) {
            return new Metadata(null, null, $readable, $writable, null, null, null, null);
        }

        if (null === $metadata->isReadable()) {
            $metadata = $metadata->withReadable($readable);
        }

        if (null === $metadata->isWritable()) {
            $metadata = $metadata->withWritable($writable);
        }

        return $metadata;
    }
}