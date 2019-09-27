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

use Gears\Event\Async\Serializer\EventSerializer;
use Gears\Event\Async\Tests\Stub\AbstractEventQueueStub;
use Gears\Event\Async\Tests\Stub\EventStub;
use PHPUnit\Framework\TestCase;

class AbstractEventQueueTest extends TestCase
{
    public function testSerialization(): void
    {
        $serializer = $this->getMockBuilder(EventSerializer::class)
            ->getMock();
        $serializer->expects(static::once())
            ->method('serialize');
        /* @var EventSerializer $serializer */

        (new AbstractEventQueueStub($serializer))->send(EventStub::instance([]));
    }
}
