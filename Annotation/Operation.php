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
 * Operation annotation.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 *
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class Operation
{
    /**
     * @var Pagination
     */
    public $pagination;

    /**
     * @var array
     */
    public $filters;

    /**
     * @var array
     */
    public $attributes;
}
