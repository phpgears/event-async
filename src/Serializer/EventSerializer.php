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

use Gears\Event\Event;

interface EventSerializer
{
    /**
     * Get serialized from event.
     *
     * @param Event $event
     *
     * @throws \Gears\Event\Async\Serializer\Exception\EventSerializationException
     *
     * @return string
     */
    public function serialize(Event $event): string;

    /**
     * Get event from serialized.
     *
     * @param string $serialized
     *
     * @throws \Gears\Event\Async\Serializer\Exception\EventSerializationException
     *
     * @return Event
     */
    public function fromSerialized(string $serialized): Event;
}
