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

namespace Gears\Event\Async\Tests\Discriminator;

use Gears\Event\Async\Discriminator\ArrayEventDiscriminator;
use Gears\Event\Async\Tests\Stub\EventStub;
use PHPUnit\Framework\TestCase;

/**
 * Array event discriminator test.
 */
class ArrayEventDiscriminatorTest extends TestCase
{
    public function testDiscriminate(): void
    {
        $eventMock = $this->getMockBuilder(EventStub::class)
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects(static::any())
            ->method('getEventType')
            ->will(static::returnValue(\get_class($eventMock)));
        /** @var \Gears\Event\Event $eventMock */
        $discriminator = new ArrayEventDiscriminator([\get_class($eventMock)]);

        static::assertTrue($discriminator->shouldEnqueue($eventMock));
        static::assertFalse($discriminator->shouldEnqueue(EventStub::instance()));
    }
}
