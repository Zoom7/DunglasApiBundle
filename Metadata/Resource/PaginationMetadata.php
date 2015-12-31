<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Metadata\Resource;

/**
 * Pagination metadata.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class PaginationMetadata
{
    /**
     * @var bool|null
     */
    private $enabled;

    /**
     * @var float|null
     */
    private $itemsPerPage;

    /**
     * @var bool|null
     */
    private $clientControlEnabled;

    /**
     * @param bool  $enabled
     * @param float $itemsPerPage
     * @param bool  $clientControlEnabled
     */
    public function __construct(bool $enabled = null, float $itemsPerPage = null, bool $clientControlEnabled = null)
    {
        $this->enabled = $enabled;
        $this->itemsPerPage = $itemsPerPage;
        $this->clientControlEnabled = $clientControlEnabled;
    }

    /**
     * Is the pagination enabled?
     *
     * @return bool|null
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Returns a new instance with the given enabled flag.
     *
     * @param bool $enabled
     *
     * @return self
     */
    public function withEnabled(bool $enabled) : self
    {
        $metadata = clone $this;
        $metadata->enabled = $enabled;

        return $metadata;
    }

    /**
     * Gets the number of items per page.
     *
     * @return float|null
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * Returns a new instance with the given number of items per page.
     *
     * @param float $itemsPerPage
     *
     * @return self
     */
    public function withItemsPerPage(float $itemsPerPage) : self
    {
        $metadata = clone $this;
        $metadata->itemsPerPage = $itemsPerPage;

        return $metadata;
    }

    /**
     * Can the client control the pagination?
     *
     * If client-side control is enabled, the pagination can be enabled and disabled on demand,
     * and the number of element by pages can be changed.
     *
     * @return bool|null
     */
    public function isClientControlEnabled()
    {
        return $this->clientControlEnabled;
    }

    /**
     * Returns a new instance with the given client control enabled flag.
     *
     * @param bool $clientControlEnabled
     *
     * @return self
     */
    public function withClientControlEnabled(bool $clientControlEnabled) : self
    {
        $metadata = clone $this;
        $metadata->clientControlEnabled = $clientControlEnabled;

        return $metadata;
    }
}
