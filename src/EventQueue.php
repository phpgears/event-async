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

use Gears\Event\Event;

interface EventQueue
{
    /**
     * Send event to queue.
     *
     * @param Event $event
     *
     * @throws \Gears\Event\Async\Exception\EventQueueException
     */
    public function send(Event $event): void;
}
