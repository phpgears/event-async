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

namespace Gears\Event\Async\Tests;

use Gears\Event\Async\ReceivedEvent;
use Gears\Event\Async\Tests\Stub\EventStub;
use PHPUnit\Framework\TestCase;

class ReceivedEventTest extends TestCase
{
    public function testOriginalEvent(): void
    {
        $originalEvent = EventStub::instance([]);

        $event = new ReceivedEvent($originalEvent);

        $this->assertSame($originalEvent, $event->getOriginalEvent());
    }

    /**
     * @expectedException \Gears\Event\Async\Exception\ReceivedEventException
     * @expectedExceptionMessage Method Gears\Event\Async\ReceivedEvent::has should not be called
     */
    public function testHasException(): void
    {
        (new ReceivedEvent(EventStub::instance([])))->has('');
    }

    /**
     * @expectedException \Gears\Event\Async\Exception\ReceivedEventException
     * @expectedExceptionMessage Method Gears\Event\Async\ReceivedEvent::get should not be called
     */
    public function testGetException(): void
    {
        (new ReceivedEvent(EventStub::instance([])))->get('');
    }

    /**
     * @expectedException \Gears\Event\Async\Exception\ReceivedEventException
     * @expectedExceptionMessage Method Gears\Event\Async\ReceivedEvent::getPayload should not be called
     */
    public function testGetPayloadException(): void
    {
        (new ReceivedEvent(EventStub::instance([])))->getPayload();
    }

    /**
     * @expectedException \Gears\Event\Async\Exception\ReceivedEventException
     * @expectedExceptionMessage Method Gears\Event\Async\ReceivedEvent::getCreatedAt should not be called
     */
    public function testGetCreatedAtException(): void
    {
        (new ReceivedEvent(EventStub::instance([])))->getCreatedAt();
    }

    /**
     * @expectedException \Gears\Event\Async\Exception\ReceivedEventException
     * @expectedExceptionMessage Method Gears\Event\Async\ReceivedEvent::reconstitute should not be called
     */
    public function testReconstituteException(): void
    {
        ReceivedEvent::reconstitute([]);
    }
}
