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

namespace Gears\Event\Async\Tests\Serializer;

use Gears\Event\Async\QueuedEvent;
use Gears\Event\Async\Serializer\Exception\EventSerializationException;
use Gears\Event\Async\Serializer\NativePhpEventSerializer;
use Gears\Event\Async\Tests\Stub\EventStub;
use PHPUnit\Framework\TestCase;

/**
 * Native PHP event serializer test.
 */
class NativePhpEventSerializerTest extends TestCase
{
    /**
     * @dataProvider serializationProvider
     *
     * @param EventStub $event
     * @param string    $serialized
     */
    public function testSerialize(EventStub $event, string $serialized): void
    {
        static::assertEquals($serialized, (new NativePhpEventSerializer())->serialize($event));
    }

    /**
     * @dataProvider queuedSerializationProvider
     *
     * @param QueuedEvent $event
     * @param string      $serialized
     */
    public function testSerializeQueued(QueuedEvent $event, string $serialized): void
    {
        static::assertEquals($serialized, (new NativePhpEventSerializer())->serialize($event));
    }

    /**
     * @dataProvider serializationProvider
     *
     * @param EventStub $event
     * @param string    $serialized
     */
    public function testDeserialize(EventStub $event, string $serialized): void
    {
        static::assertEquals(
            $event->getPayload(),
            (new NativePhpEventSerializer())->fromSerialized($serialized)->getPayload()
        );
    }

    /**
     * @dataProvider queuedSerializationProvider
     *
     * @param QueuedEvent $event
     * @param string      $serialized
     */
    public function testDeserializeQueued(QueuedEvent $event, string $serialized): void
    {
        static::assertEquals(
            $event->getWrappedEvent()->getPayload(),
            (new NativePhpEventSerializer())->fromSerialized($serialized)->getWrappedEvent()->getPayload()
        );
    }

    public function testInvalidDeserialization(): void
    {
        $this->expectException(EventSerializationException::class);
        $this->expectExceptionMessage('Event deserialization failed: could not deserialize "    "');

        (new NativePhpEventSerializer())->fromSerialized('    ');
    }

    public function testMissingEventDeserialization(): void
    {
        $this->expectException(EventSerializationException::class);
        $this->expectExceptionMessageRegExp('/^Event deserialization failed: event class ".+" cannot be found$/');

        $serialized = \version_compare(\PHP_VERSION, '7.4.0') >= 0
            ? 'O:41:"Gears\\\\Event\\\\Async\\\\Tests\\\\NotStub\\\\EventStub":0:{}'
            : 'C:41:"Gears\\\\Event\\\\Async\\\\Tests\\\\NotStub\\\\EventStub":0:{}';

        (new NativePhpEventSerializer())->fromSerialized($serialized);
    }

    public function testWrongTypeDeserialization(): void
    {
        $this->expectException(EventSerializationException::class);
        $this->expectExceptionMessage(
            'Event deserialization failed: not an instance of "Gears\Event\Event", "stdClass" given'
        );

        (new NativePhpEventSerializer())->fromSerialized('O:8:"stdClass":0:{}');
    }

    /**
     * @return mixed[][]
     */
    public function serializationProvider(): array
    {
        $stub = EventStub::instance(['parameter' => 'value']);
        $stub = $stub->withAddedMetadata(['meta' => 'data']);

        $serialized = \version_compare(\PHP_VERSION, '7.4.0') >= 0
            ? 'O:38:\\"Gears\\\\Event\\\\Async\\\\Tests\\\\Stub\\\\EventStub\\":3:{'
                . 's:7:\\"payload\\";a:1:{s:9:\\"parameter\\";s:5:\\"value\\";}'
                . 's:8:\\"metadata\\";a:1:{s:4:\\"meta\\";s:4:\\"data\\";}'
                . 's:9:\\"createdAt\\";s:32:\\"2020-01-01T00:00:00.000000+00:00\\";}'
            : 'C:38:\\"Gears\\\\Event\\\\Async\\\\Tests\\\\Stub\\\\EventStub\\":153:{a:3:{'
                . 's:7:\\"payload\\";a:1:{s:9:\\"parameter\\";s:5:\\"value\\";}'
                . 's:8:\\"metadata\\";a:1:{s:4:\\"meta\\";s:4:\\"data\\";}'
                . 's:9:\\"createdAt\\";s:32:\\"2020-01-01T00:00:00.000000+00:00\\";}}';

        return [[$stub, $serialized]];
    }

    /**
     * @return mixed[][]
     */
    public function queuedSerializationProvider(): array
    {
        $stub = EventStub::instance(['parameter' => 'value']);
        $stub = $stub->withAddedMetadata(['meta' => 'data']);

        $event = new QueuedEvent($stub);
        $reflection = new \ReflectionClass($event);
        $property = $reflection->getProperty('createdAt');
        $property->setAccessible(true);
        $property->setValue($event, \DateTimeImmutable::createFromFormat(\DateTime::ATOM, '2020-01-01T00:00:00Z'));

        $serialized = \version_compare(\PHP_VERSION, '7.4.0') >= 0
            ? 'O:29:\\"Gears\\\\Event\\\\Async\\\\QueuedEvent\\":1:{'
                . 's:12:\\"wrappedEvent\\";s:197:\\"'
                . 'O:38:\\"Gears\\\\Event\\\\Async\\\\Tests\\\\Stub\\\\EventStub\\":3:{'
                . 's:7:\\"payload\\";a:1:{s:9:\\"parameter\\";s:5:\\"value\\";}'
                . 's:8:\\"metadata\\";a:1:{s:4:\\"meta\\";s:4:\\"data\\";}'
                . 's:9:\\"createdAt\\";s:32:\\"2020-01-01T00:00:00.000000+00:00\\";'
                . '}\\";}'
            : 'C:29:\\"Gears\\\\Event\\\\Async\\\\QueuedEvent\\":231:{a:1:{'
                . 's:12:\\"wrappedEvent\\";'
                . 'C:38:\\"Gears\\\\Event\\\\Async\\\\Tests\\\\Stub\\\\EventStub\\":153:{a:3:{'
                . 's:7:\\"payload\\";a:1:{s:9:\\"parameter\\";s:5:\\"value\\";}'
                . 's:8:\\"metadata\\";a:1:{s:4:\\"meta\\";s:4:\\"data\\";}'
                . 's:9:\\"createdAt\\";s:32:\\"2020-01-01T00:00:00.000000+00:00\\";'
                . '}}}}';

        return [[$event, $serialized]];
    }
}
