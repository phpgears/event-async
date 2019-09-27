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
use Gears\Event\Async\Serializer\NativeEventSerializer;
use Gears\Event\Async\Tests\Stub\EventStub;
use PHPUnit\Framework\TestCase;

/**
 * PHP native event serializer test.
 */
class NativeEventSerializerTest extends TestCase
{
    public function testSerialize(): void
    {
        $event = EventStub::instance(['identifier' => '1234']);

        $serialized = (new NativeEventSerializer())->serialize($event);

        static::assertContains('a:1:{s:10:"identifier";s:4:"1234";}', $serialized);
    }

    public function testDeserialize(): void
    {
        $event = EventStub::instance(['identifier' => '1234']);
        $event = $event->withAddedMetadata(['meta' => 'data']);

        $deserialized = (new NativeEventSerializer())->fromSerialized(\serialize($event));

        static::assertEquals($event, $deserialized);
    }

    public function testInvalidDeserialization(): void
    {
        $this->expectException(EventSerializationException::class);
        $this->expectExceptionMessage('Invalid unserialized event');

        (new NativeEventSerializer())->fromSerialized(\serialize(new \stdClass()));
    }
}
