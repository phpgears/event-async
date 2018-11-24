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
use Gears\Event\Async\Discriminator\ClassEventDiscriminator;
use Gears\Event\Async\EventQueue;
use Gears\Event\Async\ReceivedEvent;
use Gears\Event\Async\Tests\Stub\EventStub;
use Gears\Event\EventBus;
use PHPUnit\Framework\TestCase;

class AsyncEventBusTest extends TestCase
{
    public function testShouldEnqueue(): void
    {
        $busMock = $this->getMockBuilder(EventBus::class)
            ->getMock();
        $busMock->expects($this->once())
            ->method('dispatch');
        /** @var EventBus $busMock */
        $queueMock = $this->getMockBuilder(EventQueue::class)
            ->getMock();
        $queueMock->expects($this->once())
            ->method('send');
        /** @var EventQueue $queueMock */
        $discriminatorMock = $this->getMockBuilder(ClassEventDiscriminator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $discriminatorMock->expects($this->once())
            ->method('shouldEnqueue')
            ->will($this->returnValue(true));
        /* @var \Gears\Event\Async\Discriminator\EventDiscriminator $discriminatorMock */

        (new AsyncEventBus($busMock, $queueMock, $discriminatorMock))->dispatch(EventStub::instance([]));
    }

    public function testShouldNotEnqueue(): void
    {
        $busMock = $this->getMockBuilder(EventBus::class)
            ->getMock();
        $busMock->expects($this->once())
            ->method('dispatch');
        /** @var EventBus $busMock */
        $queueMock = $this->getMockBuilder(EventQueue::class)
            ->getMock();
        $queueMock->expects($this->never())
            ->method('send');
        /** @var EventQueue $queueMock */
        $discriminatorMock = $this->getMockBuilder(ClassEventDiscriminator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $discriminatorMock->expects($this->once())
            ->method('shouldEnqueue')
            ->will($this->returnValue(false));
        /* @var \Gears\Event\Async\Discriminator\EventDiscriminator $discriminatorMock */

        (new AsyncEventBus($busMock, $queueMock, $discriminatorMock))->dispatch(EventStub::instance([]));
    }

    public function testReceivedEvent(): void
    {
        $busMock = $this->getMockBuilder(EventBus::class)
            ->getMock();
        $busMock->expects($this->once())
            ->method('dispatch');
        /** @var EventBus $busMock */
        $queueMock = $this->getMockBuilder(EventQueue::class)
            ->getMock();
        $queueMock->expects($this->never())
            ->method('send');
        /** @var EventQueue $queueMock */
        $discriminatorMock = $this->getMockBuilder(ClassEventDiscriminator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $discriminatorMock->expects($this->never())
            ->method('shouldEnqueue');
        /* @var \Gears\Event\Async\Discriminator\EventDiscriminator $discriminatorMock */

        $event = new ReceivedEvent(EventStub::instance([]));

        (new AsyncEventBus($busMock, $queueMock, $discriminatorMock))->dispatch($event);
    }
}
