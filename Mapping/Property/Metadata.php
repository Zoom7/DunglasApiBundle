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
     * @var Type
     */
    private $type;
    /**
     * @var string
     */
    private $description;
    /**
     * @var bool
     */
    private $readable;
    /**
     * @var bool
     */
    private $writable;
    /**
     * @var bool
     */
    private $readableLink;
    /**
     * @var bool
     */
    private $writableLink;
    /**
     * @var bool
     */
    private $required;
    /**
     * @var string|null
     */
    private $iri;

    /**
     * @param Type   $type
     * @param string $description
     * @param bool   $readable
     * @param bool   $writable
     * @param bool   $readableLink
     * @param bool   $writableLink
     * @param bool   $required
     * @param string $iri
     */
    public function __construct(Type $type, $description, $readable, $writable, $readableLink, $writableLink, $required, $iri)
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
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Gets description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Is readable?
     *
     * @return bool
     */
    public function isReadable()
    {
        return $this->readable;
    }

    /**
     * Is writable?
     *
     * @return bool
     */
    public function isWritable()
    {
        return $this->writable;
    }

    /**
     * Is required?
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * Is this attribute a relation to a resource?
     *
     * @return bool
     */
    public function isLink()
    {
        return $this->link;
    }

    /**
     * Should an IRI or an object be provided in write context?
     *
     * @return bool
     */
    public function isWritableLink()
    {
        return $this->writableLink;
    }

    /**
     * Is an IRI or an object generated in read context?
     *
     * @return bool
     */
    public function isReadableLink()
    {
        return $this->readableLink;
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
}
