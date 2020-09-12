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

use Gears\Event\Async\AsyncEventBus;
use Gears\Event\Async\Discriminator\EventDiscriminator;
use Gears\Event\Async\EventQueue;
use Gears\Event\Async\QueuedEvent;
use Gears\Event\Async\Tests\Stub\EventStub;
use Gears\Event\Event;
use Gears\Event\EventBus;
use PHPUnit\Framework\TestCase;

class AsyncEventBusTest extends TestCase
{
    public function testShouldEnqueue(): void
    {
        $busMock = $this->getMockBuilder(EventBus::class)
            ->getMock();
        $busMock->expects(static::once())
            ->method('dispatch');
        /** @var EventBus $busMock */
        $queueMock = $this->getMockBuilder(EventQueue::class)
            ->getMock();
        $queueMock->expects(static::once())
            ->method('send');
        /** @var EventQueue $queueMock */
        $discriminatorMock = new class() implements EventDiscriminator {
            public function shouldEnqueue(Event $event): bool
            {
                return true;
            }
        };

        (new AsyncEventBus($busMock, $queueMock, $discriminatorMock))->dispatch(EventStub::instance([]));
    }

    public function testShouldNotEnqueue(): void
    {
        $busMock = $this->getMockBuilder(EventBus::class)
            ->getMock();
        $busMock->expects(static::once())
            ->method('dispatch');
        /** @var EventBus $busMock */
        $queueMock = $this->getMockBuilder(EventQueue::class)
            ->getMock();
        $queueMock->expects(static::never())
            ->method('send');
        /** @var EventQueue $queueMock */
        $discriminatorMock = new class() implements EventDiscriminator {
            public function shouldEnqueue(Event $event): bool
            {
                return false;
            }
        };

        (new AsyncEventBus($busMock, $queueMock, $discriminatorMock))->dispatch(EventStub::instance([]));
    }

    public function testReceivedEvent(): void
    {
        $busMock = $this->getMockBuilder(EventBus::class)
            ->getMock();
        $busMock->expects(static::once())
            ->method('dispatch');
        /** @var EventBus $busMock */
        $queueMock = $this->getMockBuilder(EventQueue::class)
            ->getMock();
        $queueMock->expects(static::never())
            ->method('send');
        /** @var EventQueue $queueMock */
        $discriminatorMock = new class() implements EventDiscriminator {
            public function shouldEnqueue(Event $event): bool
            {
                return true;
            }
        };

        $event = new QueuedEvent(EventStub::instance([]));

        (new AsyncEventBus($busMock, $queueMock, $discriminatorMock))->dispatch($event);
    }
}
