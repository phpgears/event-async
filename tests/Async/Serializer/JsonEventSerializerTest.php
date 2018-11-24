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

        $this->assertContains('"payload":{"identifier":"1234"}', $serialized);
    }

    public function testDeserialize(): void
    {
        $event = new ReceivedEvent(EventStub::instance(['identifier' => '1234']));
        $eventDate = $event->getOriginalEvent()->getCreatedAt()->format('Y-m-d\TH:i:s.uP');

        $serialized = '{"class":"Gears\\\\Event\\\\Async\\\\Tests\\\\Stub\\\\EventStub",'
            . '"payload":{"identifier":"1234"},'
            . '"attributes":{"createdAt":"' . $eventDate . '"}}';

        $deserialized = (new JsonEventSerializer())->fromSerialized($serialized);

        $this->assertEquals($event, $deserialized);
    }

    /**
     * @expectedException \Gears\Event\Async\Serializer\Exception\EventSerializationException
     * @expectedExceptionMessage Malformed JSON serialized event: empty string
     */
    public function testEmptyDeserialization(): void
    {
        (new JsonEventSerializer())->fromSerialized('    ');
    }

    /**
     * @expectedException \Gears\Event\Async\Serializer\Exception\EventSerializationException
     * @expectedExceptionMessage Malformed JSON serialized event
     */
    public function testMissingPartsDeserialization(): void
    {
        (new JsonEventSerializer())
            ->fromSerialized('{"class":"Gears\\\\Event\\\\Async\\\\Tests\\\\Stub\\\\EventStub"}');
    }

    /**
     * @expectedException \Gears\Event\Async\Serializer\Exception\EventSerializationException
     * @expectedExceptionMessage Malformed JSON serialized event
     */
    public function testWrongTypeDeserialization(): void
    {
        (new JsonEventSerializer())
            ->fromSerialized('{"class":"Gears\\\\Event\\\\Async\\\\Tests\\\\Stub\\\\EventStub",'
                . '"payload":"1234","attributes":{"createdAt":"2018-01-01T00:00:00.000000+00:00"}}');
    }

    /**
     * @expectedException \Gears\Event\Async\Serializer\Exception\EventSerializationException
     * @expectedExceptionMessage Event class Gears\Unknown cannot be found
     */
    public function testMissingClassDeserialization(): void
    {
        (new JsonEventSerializer())
            ->fromSerialized('{"class":"Gears\\\\Unknown",'
            . '"payload":{"identifier":"1234"},"attributes":{"createdAt":"2018-01-01T00:00:00.000000+00:00"}}');
    }

    /**
     * @expectedException \Gears\Event\Async\Serializer\Exception\EventSerializationException
     * @expectedExceptionMessageRegExp /^Event class must implement .+\\Event, .+\\JsonEventSerializer given$/
     */
    public function testWrongClassTypeDeserialization(): void
    {
        (new JsonEventSerializer())
            ->fromSerialized('{"class":"Gears\\\\Event\\\\Async\\\\Serializer\\\\JsonEventSerializer",'
                . '"payload":{"identifier":"1234"},"attributes":{"createdAt":"2018-01-01T00:00:00.000000+00:00"}}');
    }
}
