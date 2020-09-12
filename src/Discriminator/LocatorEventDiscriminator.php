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

namespace Gears\Event\Async\Discriminator;

use Gears\Event\Event;

final class LocatorEventDiscriminator implements EventDiscriminator
{
    /**
     * @var string[]
     */
    private $eventTypes;

    /**
     * ArrayEventDiscriminator constructor.
     *
     * @param string[] $eventTypes
     */
    public function __construct(array $eventTypes)
    {
        $this->eventTypes = \array_values($eventTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function shouldEnqueue(Event $event): bool
    {
        return \in_array($event->getEventType(), $this->eventTypes, true);
    }
}
