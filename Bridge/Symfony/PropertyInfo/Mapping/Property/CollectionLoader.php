<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Bridge\Symfony\PropertyInfo\Mapping;

use Dunglas\ApiBundle\Mapping\Property\Collection;
use Dunglas\ApiBundle\Mapping\Property\Loader\Collection\LoaderInterface;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;

/**
 * PropertyInfo collection loader.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class CollectionLoader implements LoaderInterface
{
    /**
     * @var PropertyInfoExtractorInterface
     */
    private $propertyInfo;

    public function __construct(PropertyInfoExtractorInterface $propertyInfo)
    {
        $this->propertyInfo = $propertyInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection($resourceClass, array $options)
    {
        return new Collection($this->propertyInfo->getProperties($resourceClass, $options));
    }
}
