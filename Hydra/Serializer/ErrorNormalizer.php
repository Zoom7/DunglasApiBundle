<?php

/*
 * This file is part of the DunglasApiBundle package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dunglas\ApiBundle\Hydra\Serializer;

use DunglasApiBundle\Api\UrlGeneratorInterface;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Converts {@see \Exception} or {@see \Symfony\Component\Debug\Exception\FlattenException}
 * to a Hydra error representation.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 * @author Samuel ROZE <samuel.roze@gmail.com>
 */
class ErrorNormalizer implements NormalizerInterface
{
    const FORMAT = 'hydra-error';

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var bool
     */
    private $debug;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param bool                  $debug
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, $debug)
    {
        $this->urlGenerator = $urlGenerator;
        $this->debug = $debug;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        if ($object instanceof \Exception || $object instanceof FlattenException) {
            $message = $object->getMessage();

            if ($this->debug) {
                $trace = $object->getTrace();
            }
        }

        $data = [
            '@context' => $this->urlGenerator->generate('api_jsonld_context', ['shortName' => 'Error']),
            '@type' => 'Error',
            'hydra:title' => isset($context['title']) ? $context['title'] : 'An error occurred',
            'hydra:description' => isset($message) ? $message : (string) $object,
        ];

        if (isset($trace)) {
            $data['trace'] = $trace;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return self::FORMAT === $format && ($data instanceof \Exception || $data instanceof FlattenException);
    }
}
