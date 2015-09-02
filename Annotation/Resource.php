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
class Resource
{
    /**
     * @var string
     */
    public $shortName;
    /**
     * @var string
     */
    public $iri;
    /**
     * @var string
     */
    public $description = '';
    /**
     * @var Operation[]
     */
    public $itemOperations;
    /**
     * @var Operation[]
     */
    public $collectionOperations;
}
