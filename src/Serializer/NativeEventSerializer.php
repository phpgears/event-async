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

use Gears\Event\Async\ReceivedEvent;
use Gears\Event\Async\Serializer\Exception\EventSerializationException;
use Gears\Event\Event;

class NativeEventSerializer implements EventSerializer
{
    /**
     * {@inheritdoc}
     */
    public function serialize(Event $event): string
    {
        return \serialize($event);
    }

    /**
     * {@inheritdoc}
     */
    public function fromSerialized(string $serialized): ReceivedEvent
    {
        $event = \unserialize($serialized);

        if (!$event instanceof Event) {
            throw new EventSerializationException('Invalid unserialized event');
        }

        return new ReceivedEvent($event);
    }
}
