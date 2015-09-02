<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Mapping\Resource\Loader;

use Dunglas\ApiBundle\Util\ReflectionTrait;
use \phpDocumentor\Reflection\ClassReflector;
use phpDocumentor\Reflection\FileReflector;
use PropertyInfo\PropertyInfoInterface;

/**
 * Extracts descriptions from PHPDoc.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class PhpDocDescriptionLoaderDecorator implements MetadataLoaderInterface
{
    use ReflectionTrait;

    /**
     * @var FileReflector[]
     */
    private static $fileReflectors = [];
    /**
     * @var ClassReflector[]
     */
    private static $classReflectors = [];
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
    public function getMetadata($name)
    {
        $metadata = $this->loader->getMetadata($name);

        if (null === $metadata || null !== $metadata->getDescription()) {
            return $metadata;
        }

        $reflectionClass = new \ReflectionClass($name);

        if (
            ($classReflector = $this->getClassReflector($reflectionClass)) &&
            $docBlock = $classReflector->getDocBlock()
        ) {
            $classMetadata = $metadata->withDescription($docBlock->getShortDescription());
        }

        return $classMetadata;
    }

    /**
     * Gets the ClassReflector associated with this class.
     *
     * @param \ReflectionClass $reflectionClass
     *
     * @return ClassReflector|null
     */
    private function getClassReflector(\ReflectionClass $reflectionClass)
    {
        $className = $reflectionClass->name;

        if (isset(self::$classReflectors[$className])) {
            return self::$classReflectors[$className];
        }

        if (!($fileName = $reflectionClass->getFileName())) {
            return;
        }

        if (isset(self::$fileReflectors[$fileName])) {
            $fileReflector = self::$fileReflectors[$fileName];
        } else {
            $fileReflector = self::$fileReflectors[$fileName] = new FileReflector($fileName);
            $fileReflector->process();
        }

        foreach ($fileReflector->getClasses() as $classReflector) {
            $className = $classReflector->getName();
            if ('\\' === $className[0]) {
                $className = substr($className, 1);
            }

            if ($className === $reflectionClass->name) {
                return self::$classReflectors[$className] = $classReflector;
            }
        }
    }
}
