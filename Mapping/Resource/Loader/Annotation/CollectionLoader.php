<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Mapping\Resource\Loader\Annotation;

use Doctrine\Common\Annotations\Reader;
use Dunglas\ApiBundle\Annotation\Resource;
use Dunglas\ApiBundle\Mapping\Resource\Collection;
use Dunglas\ApiBundle\Mapping\Resource\Loader\CollectionLoaderInterface;

/**
 * Loads resource class annotated with the Resource annotation.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class CollectionLoader implements CollectionLoaderInterface
{
    /**
     * @var Reader
     */
    private $reader;
    /**
     * @var string[]
     */
    private $paths;

    /**
     * @param Reader   $reader
     * @param string[] $paths
     */
    public function __construct(Reader $reader, array $paths)
    {
        $this->reader = $reader;
        $this->paths = $paths;
    }

    /**
     * {@inheritdoc}
     *
     * @copyright Based on the Doctrine Project annotation driver.
     */
    public function getCollection()
    {
        $includedFiles = [];
        $classes = [];

        foreach ($this->paths as $path) {
            $iterator = new \RegexIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                ),
                '/^.+\.php$/i',
                \RecursiveRegexIterator::GET_MATCH
            );

            foreach ($iterator as $file) {
                $sourceFile = $file[0];

                if (!preg_match('(^phar:)i', $sourceFile)) {
                    $sourceFile = realpath($sourceFile);
                }

                require_once $sourceFile;

                $includedFiles[] = $sourceFile;
            }
        }

        $declared = get_declared_classes();
        foreach ($declared as $className) {
            $reflectionClass = new \ReflectionClass($className);
            $sourceFile = $reflectionClass->getFileName();
            if (in_array($sourceFile, $includedFiles) && $this->reader->getClassAnnotation($reflectionClass, Resource::class)) {
                $classes[] = $className;
            }
        }

        return new Collection($classes);
    }
}
