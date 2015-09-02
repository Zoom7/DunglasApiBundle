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
 * Pagination annotation.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 *
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class Pagination
{
    /**
     * @var bool
     */
    public $enabled = true;
    /**
     * @var float
     */
    public $itemsPerPage = 30.;
    /**
     * @var bool
     */
    public $clientControlEnabled = false;
}
