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
 * Property annotation.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 *
 * @Annotation
 * @Target({"METHOD", "PROPERTY"})
 */
final class Property
{
    /**
     * @var string
     */
    public $description;

    /**
     * @var bool
     */
    public $readable;

    /**
     * @var bool
     */
    public $writable;

    /**
     * @var bool
     */
    public $readableLink;

    /**
     * @var bool
     */
    public $writableLink;

    /**
     * @var bool
     */
    public $required;

    /**
     * @var string
     */
    public $iri;

    /**
     * @var bool
     */
    public $identifier;

    /**
     * @var array
     */
    public $attributes = [];
}
