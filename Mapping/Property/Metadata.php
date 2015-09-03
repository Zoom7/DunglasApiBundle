<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Mapping\Property;

use PropertyInfo\Type;

/**
 * Property metadata.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class Metadata
{
    /**
     * @var Type|null
     */
    private $type;
    /**
     * @var string|null
     */
    private $description;
    /**
     * @var bool|null
     */
    private $readable;
    /**
     * @var bool|null
     */
    private $writable;
    /**
     * @var bool|null
     */
    private $readableLink;
    /**
     * @var bool|null
     */
    private $writableLink;
    /**
     * @var bool|null
     */
    private $required;
    /**
     * @var string|null
     */
    private $iri;

    /**
     * @param Type|null   $type
     * @param string|null $description
     * @param bool|null   $readable
     * @param bool|null   $writable
     * @param bool|null   $readableLink
     * @param bool|null   $writableLink
     * @param bool|null   $required
     * @param string|null $iri
     */
    public function __construct(Type $type = null, $description, $readable, $writable, $readableLink, $writableLink, $required, $iri)
    {
        $this->type = $type;
        $this->description = $description;
        $this->readable = $readable;
        $this->writable = $writable;
        $this->readableLink = $readableLink;
        $this->writableLink = $writableLink;
        $this->required = $required;
        $this->iri = $iri;
    }

    /**
     * Gets type.
     *
     * @return Type|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns a new instance of Metadata with the given type.
     *
     * @param Type $type
     *
     * @return self
     */
    public function withType(Type $type)
    {
        $metadata = clone $this;
        $metadata->type = $type;

        return $metadata;
    }

    /**
     * Gets description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Returns a new instance of Metadata with the given description.
     *
     * @param string $description
     *
     * @return self
     */
    public function withDescription($description)
    {
        $metadata = clone $this;
        $metadata->description = $description;

        return $metadata;
    }

    /**
     * Is readable?
     *
     * @return bool|null
     */
    public function isReadable()
    {
        return $this->readable;
    }

    /**
     * Returns a new instance of Metadata with the given readable flag.
     *
     * @param bool $readable
     *
     * @return self
     */
    public function withReadable($readable)
    {
        $metadata = clone $this;
        $metadata->readable = $readable;

        return $metadata;
    }

    /**
     * Is writable?
     *
     * @return bool|null
     */
    public function isWritable()
    {
        return $this->writable;
    }

    /**
     * Returns a new instance of Metadata with the given writable flag.
     *
     * @param bool $writable
     *
     * @return self
     */
    public function withWritable($writable)
    {
        $metadata = clone $this;
        $metadata->writable = $writable;

        return $metadata;
    }

    /**
     * Is required?
     *
     * @return bool|null
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * Returns a new instance of Metadata with the given required flag.
     *
     * @param bool $required
     *
     * @return self
     */
    public function withRequired($required)
    {
        $metadata = clone $this;
        $metadata->required = $required;

        return $metadata;
    }

    /**
     * Should an IRI or an object be provided in write context?
     *
     * @return bool|null
     */
    public function isWritableLink()
    {
        return $this->writableLink;
    }

    /**
     * Returns a new instance of Metadata with the given writable link flag.
     *
     * @param bool $writableLink
     *
     * @return self
     */
    public function withWritableLink($writableLink)
    {
        $metadata = clone $this;
        $metadata->writableLink = $writableLink;

        return $metadata;
    }

    /**
     * Is an IRI or an object generated in read context?
     *
     * @return bool|null
     */
    public function isReadableLink()
    {
        return $this->readableLink;
    }

    /**
     * Returns a new instance of Metadata with the given readable link flag.
     *
     * @param bool $readableLink
     *
     * @return self
     */
    public function withReadableLink($readableLink)
    {
        $metadata = clone $this;
        $metadata->readableLink = $readableLink;

        return $metadata;
    }

    /**
     * Gets IRI of this attribute.
     *
     * @return string|null
     */
    public function getIri()
    {
        return $this->iri;
    }

    /**
     * Returns a new instance of Metadata with the given IRI.
     *
     * @param string|null $iri
     *
     * @return self
     */
    public function withIri($iri)
    {
        $metadata = clone $this;
        $metadata->iri = $iri;

        return $metadata;
    }
}
