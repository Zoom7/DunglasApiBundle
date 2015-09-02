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
 * Property.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 *
 * @Annotation
 * @Target({"METHOD", "PROPERTY"})
 */
class Property
{
    /**
     * @var string
     */
    public $iri;
    /**
     * @var string
     */
    public $description = '';
    /**
     * @var bool
     */
    public $readable = true;
    /**
     * @var bool
     */
    public $writable = true;
    /**
     * @var bool
     */
    public $readableLink = true;
    /**
     * @var bool
     */
    public $writableLink = true;
}
