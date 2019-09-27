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

use Gears\Event\Async\Serializer\Exception\EventSerializationException;
use Gears\Event\Async\Serializer\JsonEventSerializer;
use Gears\Event\Async\Tests\Stub\EventStub;
use PHPUnit\Framework\TestCase;

/**
 * JSON event serializer test.
 */
class JsonEventSerializerTest extends TestCase
{
    public function testSerialize(): void
    {
        $event = EventStub::instance(['identifier' => '1234']);

        $serialized = (new JsonEventSerializer())->serialize($event);

        static::assertContains('"payload":{"identifier":"1234"}', $serialized);
    }

    public function testDeserialize(): void
    {
        $event = EventStub::instance(['identifier' => '1234']);
        $event = $event->withAddedMetadata(['meta' => 'data']);
        $eventDate = $event->getCreatedAt()->format('Y-m-d\TH:i:s.uP');

        $serialized = '{"class":"Gears\\\\Event\\\\Async\\\\Tests\\\\Stub\\\\EventStub",'
            . '"payload":{"identifier":"1234"},'
            . '"createdAt":"' . $eventDate . '",'
            . '"attributes":{"metadata":{"meta":"data"}}}';

        $deserialized = (new JsonEventSerializer())->fromSerialized($serialized);

        static::assertEquals($event, $deserialized);
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
                . '"payload":"1234","createdAt":"2018-01-01T00:00:00.000000+00:00","attributes":{}}');
    }

    public function testMissingClassDeserialization(): void
    {
        $this->expectException(EventSerializationException::class);
        $this->expectExceptionMessage('Event class Gears\Unknown cannot be found');

        (new JsonEventSerializer())
            ->fromSerialized('{"class":"Gears\\\\Unknown",'
            . '"payload":{"identifier":"1234"},"createdAt":"2018-01-01T00:00:00.000000+00:00","attributes":{}}');
    }

    public function testWrongClassTypeDeserialization(): void
    {
        $this->expectException(EventSerializationException::class);
        $this->expectExceptionMessageRegExp('/^Event class must implement .+\\Event, .+\\JsonEventSerializer given$/');

        (new JsonEventSerializer())
            ->fromSerialized('{"class":"Gears\\\\Event\\\\Async\\\\Serializer\\\\JsonEventSerializer",'
                . '"payload":{"identifier":"1234"},"createdAt":"2018-01-01T00:00:00.000000+00:00","attributes":{}}');
    }
}
