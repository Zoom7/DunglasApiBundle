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
 * Pagination settings.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class PaginationSettings
{
    /**
     * @var bool
     */
    private $enabled;
    /**
     * @var float
     */
    private $itemsPerPage;
    /**
     * @var bool
     */
    private $clientControlEnabled;

    /**
     * @param bool  $enabled
     * @param float $itemsPerPage
     * @param bool  $clientControlEnabled
     */
    public function __construct($enabled, $itemsPerPage, $clientControlEnabled)
    {
        $this->enabled = $enabled;
        $this->itemsPerPage = $itemsPerPage;
        $this->clientControlEnabled = $clientControlEnabled;
    }

    /**
     * Is the pagination enabled?
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Gets the number of items per page.
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * Can the client control the pagination?
     *
     * If client-side control is enabled, the pagination can be enabled and disabled on demand,
     * and the number of element by pages can be changed.
     */
    public function isClientControlEnabled()
    {
        return $this->clientControlEnabled;
    }
}
