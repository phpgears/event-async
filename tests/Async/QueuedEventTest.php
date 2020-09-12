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

use Gears\DTO\Exception\InvalidParameterException;
use Gears\Event\Async\Exception\QueuedEventException;
use Gears\Event\Async\QueuedEvent;
use Gears\Event\Async\Tests\Stub\EventStub;
use PHPUnit\Framework\TestCase;

class QueuedEventTest extends TestCase
{
    public function testTypeException(): void
    {
        $this->expectException(QueuedEventException::class);
        $this->expectExceptionMessage('Method Gears\Event\Async\QueuedEvent::getEventType should not be called');
        (new QueuedEvent(EventStub::instance([])))->getEventType();
    }

    public function testGetException(): void
    {
        $this->expectException(InvalidParameterException::class);
        $this->expectExceptionMessageRegExp('/^Payload parameter "anotherValue" on ".+" does not exist$/');

        (new QueuedEvent(EventStub::instance([])))->get('anotherValue');
    }

    public function testGet(): void
    {
        $event = EventStub::instance([]);

        static::assertSame($event, (new QueuedEvent($event))->get('wrappedEvent'));
    }

    public function testWrappedEvent(): void
    {
        $originalEvent = EventStub::instance();

        $event = new QueuedEvent($originalEvent);

        static::assertSame($originalEvent, $event->getWrappedEvent());
    }

    public function testGetPayload(): void
    {
        $event = EventStub::instance([]);

        static::assertSame(['wrappedEvent' => $event], (new QueuedEvent($event))->getPayload());
    }

    public function testToArray(): void
    {
        $event = EventStub::instance([]);

        static::assertSame(['wrappedEvent' => $event], (new QueuedEvent($event))->toArray());
    }

    public function testGetMetadata(): void
    {
        static::assertSame([], (new QueuedEvent(EventStub::instance()))->getMetadata());
    }

    public function testGetMetadataMutateException(): void
    {
        $this->expectException(QueuedEventException::class);
        $this->expectExceptionMessage('Method Gears\Event\Async\QueuedEvent::withAddedMetadata should not be called');

        (new QueuedEvent(EventStub::instance()))->withAddedMetadata([]);
    }

    public function testGetCreatedAtException(): void
    {
        static::assertNotNull((new QueuedEvent(EventStub::instance()))->getCreatedAt());
    }

    public function testReconstitute(): void
    {
        $wrappedEvent = EventStub::reconstitute(['parameter' => 'value'], new \DateTimeImmutable('now'), []);

        $stub = QueuedEvent::reconstitute(
            new \ArrayIterator(['wrappedEvent' => $wrappedEvent]),
            new \DateTimeImmutable('now'),
            []
        );

        static::assertEquals(['parameter' => 'value'], $stub->getWrappedEvent()->getPayload());
    }

    public function testSerialization(): void
    {
        $stub = new QueuedEvent(EventStub::instance(['parameter' => 'value']));

        $serialized = \version_compare(\PHP_VERSION, '7.4.0') >= 0
            ? 'O:29:"Gears\Event\Async\QueuedEvent":1:{'
                . 's:12:"wrappedEvent";s:175:"O:38:"Gears\Event\Async\Tests\Stub\EventStub":3:{'
                . 's:7:"payload";a:1:{s:9:"parameter";s:5:"value";}'
                . 's:8:"metadata";a:0:{}'
                . 's:9:"createdAt";s:32:"2020-01-01T00:00:00.000000+00:00";'
                . '}";}'
            : 'C:29:"Gears\Event\Async\QueuedEvent":209:{a:1:{'
                . 's:12:"wrappedEvent";C:38:"Gears\\Event\\Async\\Tests\\Stub\\EventStub":131:{a:3:{'
                . 's:7:"payload";a:1:{s:9:"parameter";s:5:"value";}'
                . 's:8:"metadata";a:0:{}'
                . 's:9:"createdAt";s:32:"2020-01-01T00:00:00.000000+00:00";'
                . '}}}}';

        static::assertSame($serialized, \serialize($stub));

        /** @var QueuedEvent $unserializedStub */
        $unserializedStub = \unserialize($serialized);
        static::assertEquals(
            \get_class($stub->getWrappedEvent()),
            \get_class($unserializedStub->getWrappedEvent())
        );
        static::assertSame(
            $stub->getWrappedEvent()->getPayload(),
            $unserializedStub->getWrappedEvent()->getPayload()
        );
    }
}
