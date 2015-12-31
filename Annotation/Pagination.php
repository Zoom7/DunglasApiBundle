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
final class Pagination
{
    /**
     * @var bool|null
     */
    public $enabled;

    /**
     * @var int|null
     */
    public $itemsPerPage;

    /**
     * @var bool|null
     */
    public $clientControlEnabled;
}
