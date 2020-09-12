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
use Gears\Event\Async\Serializer\JsonEventSerializer;
use Gears\Event\Async\Tests\Stub\EventStub;
use PHPUnit\Framework\TestCase;

/**
 * JSON event serializer test.
 */
class JsonEventSerializerTest extends TestCase
{
    /**
     * @dataProvider serializationProvider
     *
     * @param EventStub $event
     * @param string    $serialized
     */
    public function testSerialize(EventStub $event, string $serialized): void
    {
        static::assertEquals($serialized, (new JsonEventSerializer())->serialize($event));
    }

    /**
     * @dataProvider queuedSerializationProvider
     *
     * @param QueuedEvent $event
     * @param string      $serialized
     */
    public function testSerializeQueued(QueuedEvent $event, string $serialized): void
    {
        static::assertEquals($serialized, (new JsonEventSerializer())->serialize($event));
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
            (new JsonEventSerializer())->fromSerialized($serialized)->getPayload()
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
            (new JsonEventSerializer())->fromSerialized($serialized)->getWrappedEvent()->getPayload()
        );
    }

    public function testEmptyDeserialization(): void
    {
        $this->expectException(EventSerializationException::class);
        $this->expectExceptionMessage('Malformed JSON serialized event: empty string');

        (new JsonEventSerializer())->fromSerialized('    ');
    }

    public function testMissingPartsDeserialization(): void
    {
        $this->expectException(EventSerializationException::class);
        $this->expectExceptionMessage('Malformed JSON serialized event');

        (new JsonEventSerializer())
            ->fromSerialized('{"class":"Gears\\\\Event\\\\Async\\\\Tests\\\\Stub\\\\EventStub"}');
    }

    public function testWrongTypeDeserialization(): void
    {
        $this->expectException(EventSerializationException::class);
        $this->expectExceptionMessage('Malformed JSON serialized event');

        (new JsonEventSerializer())
            ->fromSerialized('{"class":"Gears\\\\Event\\\\Async\\\\Tests\\\\Stub\\\\EventStub",'
                . '"payload":"1234","createdAt":"2020-01-01T00:00:00.000000+00:00","attributes":{}}');
    }

    public function testMissingClassDeserialization(): void
    {
        $this->expectException(EventSerializationException::class);
        $this->expectExceptionMessage('Event class Gears\Unknown cannot be found');

        (new JsonEventSerializer())
            ->fromSerialized('{"class":"Gears\\\\Unknown",'
            . '"payload":{"parameter":"1234"},"createdAt":"2020-01-01T00:00:00.000000+00:00","attributes":{}}');
    }

    public function testWrongClassTypeDeserialization(): void
    {
        $this->expectException(EventSerializationException::class);
        $this->expectExceptionMessageRegExp('/^Event class must implement .+\\Event, .+\\JsonEventSerializer given$/');

        (new JsonEventSerializer())
            ->fromSerialized('{"class":"Gears\\\\Event\\\\Async\\\\Serializer\\\\JsonEventSerializer",'
                . '"payload":{"parameter":"1234"},"createdAt":"2020-01-01T00:00:00.000000+00:00","attributes":{}}');
    }

    /**
     * @return mixed[][]
     */
    public function serializationProvider(): array
    {
        $stub = EventStub::instance(['parameter' => 'value']);
        $stub = $stub->withAddedMetadata(['meta' => 'data']);

        $serialized = '{"class":"Gears\\\Event\\\Async\\\Tests\\\Stub\\\EventStub",'
            . '"payload":{"parameter":"value"},'
            . '"createdAt":"2020-01-01T00:00:00.000000+00:00",'
            . '"attributes":{"metadata":{"meta":"data"}}}';

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

        $serialized = '{"class":"Gears\\\\Event\\\\Async\\\\QueuedEvent","payload":{'
            . '"wrappedEvent":"{'
            . '\"class\":\"Gears\\\\\\\\Event\\\\\\\\Async\\\\\\\\Tests\\\\\\\\Stub\\\\\\\\EventStub\",'
            . '\"payload\":{\"parameter\":\"value\"},'
            . '\"createdAt\":\"2020-01-01T00:00:00.000000+00:00\",'
            . '\"attributes\":{\"metadata\":{\"meta\":\"data\"}}}'
            . '"},'
            . '"createdAt":"2020-01-01T00:00:00.000000+00:00",'
            . '"attributes":{"metadata":[]}}';

        return [[$event, $serialized]];
    }
}
