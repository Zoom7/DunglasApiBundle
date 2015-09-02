<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Mapping\Driver\Annotation;

use Dunglas\ApiBundle\Mapping\Property\Collection;
use Dunglas\ApiBundle\Mapping\Property\Loader\CollectionLoaderInterface;

/**
 * Loads properties annotated with the Property annotation.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class ResourcePropertyCollectionLoader implements CollectionLoaderInterface
{
    private $reader;

    public function __construct(Reader $reader)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getCollection($resourceClass, array $options)
    {
        $reflectionClass = new \ReflectionClass($resourceClass);

    }
}