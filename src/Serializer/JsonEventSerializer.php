<?php

/*
 * event-async (https://github.com/phpgears/event-async).
 * Async decorator for Event bus.
 *
 * @license MIT
 * @link https://github.com/phpgears/event-async
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

declare(strict_types=1);

namespace Gears\Event\Async\Serializer;

use Gears\Event\Async\Serializer\Exception\EventSerializationException;
use Gears\Event\Event;

final class JsonEventSerializer implements EventSerializer
{
    /**
     * JSON encoding options.
     * Preserve float values and encode &, ', ", < and > characters in the resulting JSON.
     */
    private const JSON_ENCODE_OPTIONS = \JSON_UNESCAPED_UNICODE
        | \JSON_UNESCAPED_SLASHES
        | \JSON_PRESERVE_ZERO_FRACTION
        | \JSON_HEX_AMP
        | \JSON_HEX_APOS
        | \JSON_HEX_QUOT
        | \JSON_HEX_TAG;

    /**
     * JSON decoding options.
     * Decode large integers as string values.
     */
    private const JSON_DECODE_OPTIONS = \JSON_BIGINT_AS_STRING;

    /**
     * \DateTime::RFC3339_EXTENDED cannot handle microseconds on \DateTimeImmutable::createFromFormat.
     *
     * @see https://stackoverflow.com/a/48949373
     */
    private const DATE_RFC3339_EXTENDED = 'Y-m-d\TH:i:s.uP';

    /**
     * {@inheritdoc}
     */
    public function serialize(Event $event): string
    {
        $serialized = \json_encode(
            [
                'class' => \get_class($event),
                'payload' => $event->getPayload(),
                'createdAt' => $event->getCreatedAt()->format(static::DATE_RFC3339_EXTENDED),
                'attributes' => $this->getSerializationAttributes($event),
            ],
            static::JSON_ENCODE_OPTIONS
        );

        // @codeCoverageIgnoreStart
        if ($serialized === false || \json_last_error() !== \JSON_ERROR_NONE) {
            throw new EventSerializationException(\sprintf(
                'Error serializing event %s due to %s',
                \get_class($event),
                \lcfirst(\json_last_error_msg())
            ));
        }
        // @codeCoverageIgnoreEnd

        return $serialized;
    }

    /**
     * Get serialization attributes.
     *
     * @param Event $event
     *
     * @return array<string, mixed>
     */
    private function getSerializationAttributes(Event $event): array
    {
        return [
            'metadata' => $event->getMetadata(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fromSerialized(string $serialized): Event
    {
        ['class' => $eventClass, 'payload' => $payload, 'createdAt' => $createdAt, 'attributes' => $attributes] =
            $this->getEventDefinition($serialized);

        if (!\class_exists($eventClass)) {
            throw new EventSerializationException(\sprintf('Event class %s cannot be found', $eventClass));
        }

        if (!\in_array(Event::class, \class_implements($eventClass), true)) {
            throw new EventSerializationException(\sprintf(
                'Event class must implement %s, %s given',
                Event::class,
                $eventClass
            ));
        }

        $createdAt = \DateTimeImmutable::createFromFormat(self::DATE_RFC3339_EXTENDED, $createdAt);

        // @codeCoverageIgnoreStart
        try {
            /* @var Event $eventClass */
            return $eventClass::reconstitute($payload, $createdAt, $this->getDeserializationAttributes($attributes));
        } catch (\Exception $exception) {
            throw new EventSerializationException('Error reconstituting event', 0, $exception);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Get event definition from serialization.
     *
     * @param string $serialized
     *
     * @throws EventSerializationException
     *
     * @return array<string, mixed>
     */
    private function getEventDefinition(string $serialized): array
    {
        $definition = $this->getDeserializationDefinition($serialized);

        if (!isset($definition['class'], $definition['payload'], $definition['createdAt'], $definition['attributes'])
            || \count(\array_diff(\array_keys($definition), ['class', 'payload', 'createdAt', 'attributes'])) !== 0
            || !\is_string($definition['class'])
            || !\is_array($definition['payload'])
            || !\is_string($definition['createdAt'])
            || !\is_array($definition['attributes'])
        ) {
            throw new EventSerializationException('Malformed JSON serialized event');
        }

        return $definition;
    }

    /**
     * Get deserialization definition.
     *
     * @param string $serialized
     *
     * @return array<string, mixed>
     */
    private function getDeserializationDefinition(string $serialized): array
    {
        if (\trim($serialized) === '') {
            throw new EventSerializationException('Malformed JSON serialized event: empty string');
        }

        $definition = \json_decode($serialized, true, 512, static::JSON_DECODE_OPTIONS);

        // @codeCoverageIgnoreStart
        if ($definition === null || \json_last_error() !== \JSON_ERROR_NONE) {
            throw new EventSerializationException(\sprintf(
                'Event deserialization failed due to error %s: %s',
                \json_last_error(),
                \lcfirst(\json_last_error_msg())
            ));
        }
        // @codeCoverageIgnoreEnd

        return $definition;
    }

    /**
     * Get deserialization attributes.
     *
     * @param array<string, mixed> $attributes
     *
     * @return array<string, mixed>
     */
    private function getDeserializationAttributes(array $attributes): array
    {
        return [
            'metadata' => $attributes['metadata'],
        ];
    }
}
