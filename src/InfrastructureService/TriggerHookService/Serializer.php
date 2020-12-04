<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\InfrastructureService\TriggerHookService;

use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Stamp\NonSendableStampInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;


class Serializer implements SerializerInterface
{
    private $serializer;
    private $format;
    private $messageClass;

    public function __construct(string $messageClass, SymfonySerializerInterface $serializer = null, string $format = 'json')
    {
        $this->serializer = $serializer ?? self::create()->serializer;
        $this->format = $format;
        $this->messageClass = $messageClass;
    }

    public static function create(): self
    {
        if (!class_exists(SymfonySerializer::class)) {
            throw new LogicException(sprintf('The "%s" class requires Symfony\'s Serializer component. Try running "composer require symfony/serializer" or use "%s" instead.', __CLASS__, PhpSerializer::class));
        }

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ArrayDenormalizer(), new ObjectNormalizer()];
        $serializer = new SymfonySerializer($normalizers, $encoders);

        return new self($serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function decode(array $encodedEnvelope): Envelope
    {
        if (empty($encodedEnvelope['body'])) {
            throw new MessageDecodingFailedException('Encoded envelope should have at least a "body"');
        }

        try {
            $message = $this->serializer->deserialize($encodedEnvelope['body'], $this->messageClass, $this->format);
        } catch (ExceptionInterface $e) {
            throw new MessageDecodingFailedException('Could not decode message: ' . $e->getMessage(), $e->getCode(), $e);
        }

        return new Envelope($message, []);
    }

    /**
     * {@inheritdoc}
     */
    public function encode(Envelope $envelope): array
    {
        $envelope = $envelope->withoutStampsOfType(NonSendableStampInterface::class);

        return [
            'body' => $this->serializer->serialize($envelope->getMessage(), $this->format),
            'headers' => $this->getContentTypeHeader()
        ];
    }

    private function getContentTypeHeader(): array
    {
        return ['Content-Type' => 'application/json'];
    }

}
