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

namespace Gears\Event\Async\Tests\Stub;

use Gears\Event\Async\AbstractEventQueue;
use Gears\Event\Event;

/**
 * AbstractEventQueueStub stub class.
 */
class AbstractEventQueueStub extends AbstractEventQueue
{
    /**
     * {@inheritdoc}
     */
    public function send(Event $event): void
    {
        $this->getSerializedEvent($event);

        // noop, should enqueue event
    }
}
