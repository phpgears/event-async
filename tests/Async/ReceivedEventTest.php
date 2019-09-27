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

use Gears\Event\Async\Exception\ReceivedEventException;
use Gears\Event\Async\ReceivedEvent;
use Gears\Event\Async\Tests\Stub\EventStub;
use PHPUnit\Framework\TestCase;

class ReceivedEventTest extends TestCase
{
    public function testOriginalEvent(): void
    {
        $originalEvent = EventStub::instance();

        $event = new ReceivedEvent($originalEvent);

        static::assertSame($originalEvent, $event->getOriginalEvent());
    }

    public function testHasException(): void
    {
        $this->expectException(ReceivedEventException::class);
        $this->expectExceptionMessage('Method Gears\Event\Async\ReceivedEvent::has should not be called');

        (new ReceivedEvent(EventStub::instance()))->has('');
    }

    public function testGetException(): void
    {
        $this->expectException(ReceivedEventException::class);
        $this->expectExceptionMessage('Method Gears\Event\Async\ReceivedEvent::get should not be called');

        (new ReceivedEvent(EventStub::instance()))->get('');
    }

    public function testGetPayloadException(): void
    {
        $this->expectException(ReceivedEventException::class);
        $this->expectExceptionMessage('Method Gears\Event\Async\ReceivedEvent::getPayload should not be called');

        (new ReceivedEvent(EventStub::instance()))->getPayload();
    }

    public function testGetMetadataException(): void
    {
        $this->expectException(ReceivedEventException::class);
        $this->expectExceptionMessage('Method Gears\Event\Async\ReceivedEvent::getMetadata should not be called');

        (new ReceivedEvent(EventStub::instance()))->getMetadata();
    }

    public function testGetMetadataMutateException(): void
    {
        $this->expectException(ReceivedEventException::class);
        $this->expectExceptionMessage('Method Gears\Event\Async\ReceivedEvent::withAddedMetadata should not be called');

        (new ReceivedEvent(EventStub::instance()))->withAddedMetadata([]);
    }

    public function testGetCreatedAtException(): void
    {
        $this->expectException(ReceivedEventException::class);
        $this->expectExceptionMessage('Method Gears\Event\Async\ReceivedEvent::getCreatedAt should not be called');

        (new ReceivedEvent(EventStub::instance()))->getCreatedAt();
    }

    public function testReconstituteException(): void
    {
        $this->expectException(ReceivedEventException::class);
        $this->expectExceptionMessage('Method Gears\Event\Async\ReceivedEvent::reconstitute should not be called');

        ReceivedEvent::reconstitute([], new \DateTimeImmutable('now'));
    }
}
