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

use Gears\Event\Async\Discriminator\EventDiscriminator;
use Gears\Event\Event;
use Gears\Event\EventBus;

class AsyncEventBus implements EventBus
{
    /**
     * Wrapped event bus.
     *
     * @var EventBus
     */
    private $wrappedEventBus;

    /**
     * Event queue.
     *
     * @var EventQueue
     */
    private $queue;

    /**
     * Event discriminator.
     *
     * @var EventDiscriminator
     */
    private $discriminator;

    /**
     * AsyncEventBus constructor.
     *
     * @param EventBus           $wrappedEventBus
     * @param EventQueue         $queue
     * @param EventDiscriminator $discriminator
     */
    public function __construct(
        EventBus $wrappedEventBus,
        EventQueue $queue,
        EventDiscriminator $discriminator
    ) {
        $this->wrappedEventBus = $wrappedEventBus;
        $this->discriminator = $discriminator;
        $this->queue = $queue;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Gears\Event\Async\Exception\EventQueueException
     */
    final public function dispatch(Event $event): void
    {
        if (!$event instanceof ReceivedEvent && $this->discriminator->shouldEnqueue($event)) {
            $this->queue->send($event);
        }

        if ($event instanceof ReceivedEvent) {
            $event = $event->getOriginalEvent();
        }

        $this->wrappedEventBus->dispatch($event);
    }
}
