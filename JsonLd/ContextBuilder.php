<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\JsonLd;

use Dunglas\ApiBundle\Metadata\Property\Factory\CollectionMetadataFactoryInterface as PropertyCollectionMetadataFactoryInterface;
use Dunglas\ApiBundle\Metadata\Property\Factory\ItemMetadataFactoryInterface as PropertyItemMetadataFactoryInterface;
use Dunglas\ApiBundle\Metadata\Resource\Factory\CollectionMetadataFactoryInterface as ResourceCollectionMetadataFactoryInterface;
use Dunglas\ApiBundle\Metadata\Resource\Factory\ItemMetadataFactoryInterface as ResourceItemMetadataFactoryInterface;
use Dunglas\ApiBundle\Api\UrlGeneratorInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * {@inheritdoc}
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class ContextBuilder implements ContextBuilderInterface
{
    /**
     * @var ResourceCollectionMetadataFactoryInterface
     */
    private $resourceCollectionMetadataFactory;

    /**
     * @var ResourceItemMetadataFactoryInterface
     */
    private $resourceItemMetadataFactory;

    /**
     * @var PropertyCollectionMetadataFactoryInterface
     */
    private $propertyCollectionMetadataFactory;

    /**
     * @var PropertyItemMetadataFactoryInterface
     */
    private $propertyItemMetadataFactory;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var NameConverterInterface
     */
    private $nameConverter;

    public function __construct(ResourceCollectionMetadataFactoryInterface $resourceCollectionMetadataFactory, ResourceItemMetadataFactoryInterface $resourceItemMetadataFactory, PropertyCollectionMetadataFactoryInterface $propertyCollectionMetadataFactory, PropertyItemMetadataFactoryInterface $propertyItemMetadataFactory, UrlGeneratorInterface $urlGenerator, NameConverterInterface $nameConverter = null)
    {
        $this->resourceCollectionMetadataFactory = $resourceCollectionMetadataFactory;
        $this->resourceItemMetadataFactory = $resourceItemMetadataFactory;
        $this->propertyCollectionMetadataFactory = $propertyCollectionMetadataFactory;
        $this->propertyItemMetadataFactory = $propertyItemMetadataFactory;
        $this->urlGenerator = $urlGenerator;
        $this->nameConverter = $nameConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseContext(int $referenceType = UrlGeneratorInterface::ABS_URL) : array
    {
        return [
            '@vocab' => $this->urlGenerator->generate('api_hydra_vocab', [], UrlGeneratorInterface::ABS_URL).'#',
            'hydra' => self::HYDRA_NS,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getEntrypointContext(int $referenceType = UrlGeneratorInterface::ABS_PATH) : array
    {
        $context = $this->getBaseContext($referenceType);

        foreach ($this->resourceCollectionMetadataFactory->create() as $resourceClass) {
            $itemMetadata = $this->resourceItemMetadataFactory->create($resourceClass);

            $resourceName = lcfirst($itemMetadata->getShortName());

            $context[$resourceName] = [
                '@id' => 'Entrypoint/'.$resourceName,
                '@type' => '@id',
            ];
        }

        return $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceContext(string $resourceClass, int $referenceType = UrlGeneratorInterface::ABS_PATH) : array
    {
        $context = $this->getBaseContext($referenceType, $referenceType);
        $itemMetadata = $this->resourceItemMetadataFactory->create($resourceClass);
        $prefixedShortName = sprintf('#%s', $itemMetadata->getShortName());

        foreach ($this->propertyCollectionMetadataFactory->create($resourceClass) as $propertyName) {
            $propertyItemMetadata = $this->propertyItemMetadataFactory->create($resourceClass, $propertyName);

            if ($propertyItemMetadata->isIdentifier() && !$propertyItemMetadata->isWritable()) {
                continue;
            }

            $convertedName = $this->nameConverter ? $this->nameConverter->normalize($propertyName) : $propertyName;

            if (!$id = $propertyItemMetadata->getIri()) {
                $id = sprintf('%s/%s', $prefixedShortName, $convertedName);
            }

            if (!$propertyItemMetadata->isReadableLink()) {
                $context[$convertedName] = [
                    '@id' => $id,
                    '@type' => '@id',
                ];
            } else {
                $context[$convertedName] = $id;
            }
        }

        return $context;
    }

    public function getResourceContextUri(string $resourceClass, int $referenceType = UrlGeneratorInterface::ABS_PATH) : string
    {
        $itemMetadata = $this->resourceItemMetadataFactory->create($resourceClass);

        return $this->urlGenerator->generate('api_jsonld_context', ['shortName' => $itemMetadata->getShortName()]);
    }
}
