<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Mapping\Resource;

/**
 * Operation.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class Operation
{
    /**
     * @var array
     */
    private $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Gets operation custom attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
       return $this->attributes;
    }
}
