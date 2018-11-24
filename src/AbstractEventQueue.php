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

namespace Gears\Event\Async;

use Gears\Event\Async\Serializer\EventSerializer;
use Gears\Event\Event;

abstract class AbstractEventQueue implements EventQueue
{
    /**
     * Event serializer.
     *
     * @var EventSerializer
     */
    private $serializer;

    /**
     * AbstractEventQueue constructor.
     *
     * @param EventSerializer $serializer
     */
    public function __construct(EventSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Get serialized event.
     *
     * @param Event $event
     *
     * @return string
     */
    final protected function getSerializedEvent(Event $event): string
    {
        return $this->serializer->serialize($event);
    }
}
