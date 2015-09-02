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
class Operation
{
    /**
     * @var array
     */
    private $attributes;
    /**
     * @var PaginationSettings
     */
    private $paginationSettings;

    public function __construct(array $attributes, PaginationSettings $paginationSettings)
    {
        $this->attributes = $attributes;
        $this->paginationSettings = $paginationSettings;
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

    /**
     * Gets pagination settings if applicable.
     *
     * @return PaginationSettings|null
     */
    public function getPaginationSettings()
    {
        return $this->paginationSettings;
    }
}
