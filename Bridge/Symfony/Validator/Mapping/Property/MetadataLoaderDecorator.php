<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Bridge\Symfony\Validator\Mapping\Property;

use Dunglas\ApiBundle\Mapping\Property\Loader\Metadata\LoaderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;

/**
 * Decorates a metadata loader using the validator.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class MetadataLoaderDecorator implements LoaderInterface
{
    /**
     * @var string[] A list of constraint classes making the entity required.
     */
    const REQUIRED_CONSTRAINTS = [
        'Symfony\Component\Validator\Constraints\NotBlank',
        'Symfony\Component\Validator\Constraints\NotNull',
    ];

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    public function __construct(LoaderInterface $loader, MetadataFactoryInterface $metadataFactory)
    {
        $this->loader = $loader;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * Is this constraint making the related property required?
     *
     * @param Constraint $constraint
     *
     * @return bool
     */
    private function isRequired(Constraint $constraint)
    {
        foreach (self::REQUIRED_CONSTRAINTS as $requiredConstraint) {
            if ($constraint instanceof $requiredConstraint) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($resourceClass, $name, array $options)
    {
        $metadata = $this->loader->getMetadata($resourceClass, $name, $options);
        if (null !== $metadata->isRequired()) {
            return $metadata;
        }

        $validatorClassMetadata = $this->metadataFactory->getMetadataFor($resourceClass);

        foreach ($validatorClassMetadata->getPropertyMetadata($name) as $validatorPropertyMetadata) {
            if (isset($options['validation_groups'])) {
                foreach ($options['validation_groups'] as $validationGroup) {
                    if (!is_string($validationGroup)) {
                        continue;
                    }

                    foreach ($validatorPropertyMetadata->findConstraints($validationGroup) as $constraint) {
                        if ($this->isRequired($constraint)) {
                            return $metadata->withRequired(true);
                        }
                    }
                }

                return $metadata->withRequired(false);
            }

            foreach ($validatorPropertyMetadata->findConstraints($validatorClassMetadata->getDefaultGroup()) as $constraint) {
                if ($this->isRequired($constraint)) {
                    return $metadata->withRequired(true);
                }
            }

            return $metadata->withRequired(false);
        }

        return $metadata->withRequired(false);
    }
}
