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

interface EventDiscriminator
{
    /**
     * Should event be enqueued.
     *
     * @param Event $event
     *
     * @return bool
     */
    public function shouldEnqueue(Event $event): bool;
}
