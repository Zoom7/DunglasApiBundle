<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\JsonLd\Event;

use Dunglas\ApiBundle\Api\ResourceInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * ContextBuilderEvent.
 *
 * @author Luc Vieillescazes <luc@vieillescazes.net>
 */
class ContextBuilderEvent extends Event
{
    /**
     * @var array
     */
    private $context;
    /**
     * @var string|null
     */
    private $resourceClass;

    /**
     * @param array             $context
     * @param string            $resourceClass
     */
    public function __construct(array $context, string $resourceClass = null)
    {
        $this->context = $context;
        $this->resourceClass = $resourceClass;
    }

    /**
     * @return string|null
     */
    public function getResourceClass()
    {
        return $this->resourceClass;
    }

    /**
     * @return array
     */
    public function getContext() : array
    {
        return $this->context;
    }

    /**
     * @param array $context
     */
    public function setContext(array $context)
    {
        $this->context = $context;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return ContextBuilderEvent
     */
    public function addToContext(string $key, $value) : self
    {
        $this->context[$key] = $value;

        return $this;
    }
}
