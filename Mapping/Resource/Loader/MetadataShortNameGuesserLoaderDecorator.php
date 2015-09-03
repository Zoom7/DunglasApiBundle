<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Mapping\Resource\Loader;

/**
 * Guesses the short name if not set.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class MetadataShortNameGuesserLoaderDecorator implements MetadataLoaderInterface
{
    /**
     * @var MetadataLoaderInterface
     */
    private $loader;

    public function __construct(MetadataLoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($name)
    {
        $metadata = $this->loader->getMetadata($name);

        if (null === $metadata || null !== $metadata->getShortName()) {
            return $metadata;
        }

        $name = $metadata->getName();

        return $metadata->withShortName(substr($name, strrpos($name, '\\') + 1));
    }
}
