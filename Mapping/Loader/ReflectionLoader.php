<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Mapping\Loader;

use Dunglas\ApiBundle\Mapping\Factory\AttributeMetadataFactoryInterface;
use Dunglas\ApiBundle\Mapping\AttributeMetadataInterface;
use Dunglas\ApiBundle\Mapping\ClassMetadataInterface;
use Dunglas\ApiBundle\Mapping\Factory\ClassMetadataFactoryInterface;

/**
 * Uses reflection to populate attributes.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class ReflectionLoader implements LoaderInterface, ClassMetadataFactoryAwareInterface
{
    /**
     * @var string
     */
    private $defaultIdentifierName;
    /**
     * @var AttributeMetadataFactoryInterface
     */
    private $attributeMetadataFactory;

    /**
     * @param AttributeMetadataFactoryInterface $attributeMetadataFactory
     * @param string                            $defaultIdentifierName
     */
    public function __construct(AttributeMetadataFactoryInterface $attributeMetadataFactory, $defaultIdentifierName = 'id')
    {
        $this->attributeMetadataFactory = $attributeMetadataFactory;
        $this->defaultIdentifierName = $defaultIdentifierName;
    }

    /**
     * {@inheritdoc}
     */
    public function setClassMetadataFactory(ClassMetadataFactoryInterface $classMetadataFactory)
    {
        $this->attributeMetadataFactory->setClassMetadataFactory($classMetadataFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function loadClassMetadata(ClassMetadataInterface $classMetadata, array $options = []) {
        if (isset($options['normalization_groups']) && isset($options['denormalization_groups'])) {
            return $classMetadata;
        }

        $reflectionClass = $classMetadata->getReflectionClass();
        $classMetadata = $this->populateFromPublicMethods($reflectionClass, $classMetadata, $options);
        $classMetadata = $this->populateFromPublicProperties($reflectionClass, $classMetadata, $options);

        return $classMetadata;
    }

    /**
     * Pouplates class metadata using public methods.
     *
     * @param \ReflectionClass       $reflectionClass
     * @param ClassMetadataInterface $classMetadata
     * @param array                  $options
     *
     * @return ClassMetadataInterface
     */
    private function populateFromPublicMethods(\ReflectionClass $reflectionClass, ClassMetadataInterface $classMetadata, array $options)
    {
        // Methods
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            $numberOfRequiredParameters = $reflectionMethod->getNumberOfRequiredParameters();
            $methodName = $reflectionMethod->name;

            $newClassMetadata = $this->populateFromSetter(
                $classMetadata, $methodName, $numberOfRequiredParameters, $options
            );

            if ($newClassMetadata) {
                $classMetadata = $newClassMetadata;
                continue;
            }

            if (0 !== $numberOfRequiredParameters) {
                continue;
            }

            $newClassMetadata = $this->populateFromGetterAndHasser($classMetadata, $methodName, $options);

            if ($newClassMetadata) {
                $classMetadata = $newClassMetadata;
                continue;
            }

            $newClassMetadata = $this->populateFromIsser($classMetadata, $methodName, $options);
            if ($newClassMetadata) {
                $classMetadata = $newClassMetadata;
            }
        }

        return $classMetadata;
    }

    /**
     * Populates attributes from setter methods.
     *
     * @param ClassMetadataInterface $classMetadata
     * @param string                 $methodName
     * @param int                    $numberOfRequiredParameters
     * @param array                  $options
     *
     * @return ClassMetadataInterface|null
     */
    private function populateFromSetter(ClassMetadataInterface $classMetadata, $methodName, $numberOfRequiredParameters, array $options) {
        if (
            isset($options['normalization_groups']) ||
            1 !== $numberOfRequiredParameters ||
            !preg_match('/^(set|add|remove)(.+)$/i', $methodName, $matches)
        ) {
            return;
        }

        $attributeName = lcfirst($matches[2]);
        $attributeMetadata = $this
            ->attributeMetadataFactory
            ->getAttributeMetadataFor($classMetadata, $attributeName, $options)
            ->withWritable(true)
        ;

        return $this->addAttributeMetadata($classMetadata, $attributeMetadata, $attributeName);
    }

    /**
     * Populates attributes from getters and hassers.
     *
     * @param ClassMetadataInterface $classMetadata
     * @param string                 $methodName
     * @param array                  $options
     *
     * @return ClassMetadataInterface|null
     */
    private function populateFromGetterAndHasser(ClassMetadataInterface $classMetadata, $methodName, array $options) {
        if (
            isset($options['normalization_groups']) ||
            (0 !== strpos($methodName, 'get') && 0 !== strpos($methodName, 'has'))
        ) {
            return;
        }

        $attributeName = lcfirst(substr($methodName, 3));
        $attributeMetadata = $this
            ->attributeMetadataFactory
            ->getAttributeMetadataFor($classMetadata, $attributeName, $options)
            ->withReadable(true);

        return $this->addAttributeMetadata($classMetadata, $attributeMetadata, $attributeName);
    }

    /**
     * Populates attributes from issers.
     *
     * @param ClassMetadataInterface $classMetadata
     * @param string                 $methodName
     * @param array                  $options
     *
     * @return ClassMetadataInterface|null
     */
    private function populateFromIsser(ClassMetadataInterface $classMetadata, $methodName, array $options)
    {
        if ($options['normalization_groups'] || 0 !== strpos($methodName, 'is')) {
            return;
        }

        $attributeName = lcfirst(substr($methodName, 2));
        $attributeMetadata = $this
            ->attributeMetadataFactory
            ->getAttributeMetadataFor($classMetadata, $attributeName, $options)
            ->withReadable(true)
        ;

        return $this->addAttributeMetadata($classMetadata, $attributeMetadata, $attributeName);
    }

    /**
     * Populates class metadata from public properties.
     *
     * @param \ReflectionClass       $reflectionClass
     * @param ClassMetadataInterface $classMetadata
     * @param array                  $options
     *
     * @return ClassMetadataInterface
     */
    private function populateFromPublicProperties(\ReflectionClass $reflectionClass, ClassMetadataInterface $classMetadata, array $options)
    {
        foreach ($reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $attributeName = $reflectionProperty->name;
            $attributeMetadata = $this
                ->attributeMetadataFactory
                ->getAttributeMetadataFor($classMetadata, $attributeName, $options);

            if (isset($options['normalization_groups'])) {
                $attributeMetadata = $attributeMetadata->withReadable(true);
                $classMetadata = $this->addAttributeMetadata($classMetadata, $attributeMetadata, $attributeName);
            }

            if (isset($options['denormalization_groups'])) {
                $attributeMetadata = $attributeMetadata->withWritable(true);
                $classMetadata = $this->addAttributeMetadata($classMetadata, $attributeMetadata, $attributeName);
            }
        }

        return $classMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllClassMetadata()
    {
        return [];
    }

    /**
     * Adds an attribute metadata to the class metadata and set it as identifier if applicable.
     *
     * @param ClassMetadataInterface     $classMetadata
     * @param AttributeMetadataInterface $attributeMetadata
     * @param string                     $attributeName
     *
     * @return ClassMetadataInterface
     */
    private function addAttributeMetadata(ClassMetadataInterface $classMetadata, AttributeMetadataInterface $attributeMetadata, $attributeName)
    {
        $classMetadata = $classMetadata->withAttributeMetadata($attributeName, $attributeMetadata);

        if ($this->defaultIdentifierName === $attributeName) {
            $classMetadata = $classMetadata->withIdentifierName($this->defaultIdentifierName);
        }

        return $classMetadata;
    }
}
