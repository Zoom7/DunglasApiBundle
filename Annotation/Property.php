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
     * @var string|null
     */
    public $description;
    /**
     * @var bool|null
     */
    public $readable;
    /**
     * @var bool|null
     */
    public $writable;
    /**
     * @var bool|null
     */
    public $readableLink;
    /**
     * @var bool|null
     */
    public $writableLink;
    /**
     * @var bool|null
     */
    public $required;
    /**
     * @var string|null
     */
    public $iri;
}
