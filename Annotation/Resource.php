<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Annotation;

/**
 * Resource annotation.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 *
 * @Annotation
 * @Target({"CLASS"})
 */
final class Resource
{
    /**
     * @var string|null
     */
    public $shortName;

    /**
     * @var string|null
     */
    public $description;

    /**
     * @var string|null
     */
    public $iri;

    /**
     * @var array|null
     */
    public $itemOperations;

    /**
     * @var array|null
     */
    public $collectionOperations;

    /**
     * @var array
     */
    public $attributes;
}
