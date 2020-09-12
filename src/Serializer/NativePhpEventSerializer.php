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

/**
 * @see https://github.com/symfony/messenger/blob/master/Transport/Serialization/PhpSerializer.php
 */
final class NativePhpEventSerializer implements EventSerializer
{
    /**
     * {@inheritdoc}
     */
    public function serialize(Event $event): string
    {
        return \addslashes(\serialize($event));
    }

    /**
     * {@inheritdoc}
     */
    public function fromSerialized(string $serialized): Event
    {
        $serialized = \stripslashes($serialized);

        $unserializeException = new EventSerializationException(
            \sprintf('Event deserialization failed: could not deserialize "%s"', $serialized)
        );
        $unserializeHandler = \ini_set('unserialize_callback_func', self::class . '::handleUnserializeCallback');
        $prevErrorHandler = \set_error_handler(
            function ($type, $msg, $file, $line, $context = []) use (&$prevErrorHandler, $unserializeException) {
                if (__FILE__ === $file) {
                    throw $unserializeException;
                }

                return $prevErrorHandler !== null ? $prevErrorHandler($type, $msg, $file, $line, $context) : false;
            }
        );

        try {
            $event = \unserialize($serialized);
        } finally {
            \restore_error_handler();

            if ($unserializeHandler !== false) {
                \ini_set('unserialize_callback_func', $unserializeHandler);
            }
        }

        if (!\is_object($event) || !$event instanceof Event) {
            throw new EventSerializationException(\sprintf(
                'Event deserialization failed: not an instance of "%s", "%s" given',
                Event::class,
                \is_object($event) ? \get_class($event) : \gettype($event)
            ));
        }

        return $event;
    }

    /**
     * Called if an undefined class should be instantiated during unserializing.
     * To prevent getting an incomplete object "__PHP_Incomplete_Class".
     *
     * @param string $class
     */
    public static function handleUnserializeCallback(string $class): void
    {
        throw new EventSerializationException(
            \sprintf('Event deserialization failed: event class "%s" cannot be found', $class)
        );
    }
}
