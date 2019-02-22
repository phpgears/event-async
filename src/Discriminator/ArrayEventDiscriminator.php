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

final class ArrayEventDiscriminator implements EventDiscriminator
{
    /**
     * @var string[]
     */
    private $events;

    /**
     * ArrayEventDiscriminator constructor.
     *
     * @param string[] $events
     */
    public function __construct(array $events)
    {
        $this->events = \array_values($events);
    }

    /**
     * {@inheritdoc}
     */
    public function shouldEnqueue(Event $event): bool
    {
        return \in_array(\get_class($event), $this->events, true);
    }
}
