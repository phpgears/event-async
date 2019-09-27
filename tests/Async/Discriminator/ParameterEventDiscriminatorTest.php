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

use Gears\Event\Async\Discriminator\ParameterEventDiscriminator;
use Gears\Event\Async\Tests\Stub\EventStub;
use PHPUnit\Framework\TestCase;

/**
 * Parameter event discriminator test.
 */
class ParameterEventDiscriminatorTest extends TestCase
{
    public function testDiscriminateParameter(): void
    {
        $discriminator = new ParameterEventDiscriminator('identifier');

        static::assertTrue($discriminator->shouldEnqueue(EventStub::instance(['identifier' => null])));
        static::assertFalse($discriminator->shouldEnqueue(EventStub::instance([])));
    }

    public function testDiscriminateParameterValue(): void
    {
        $discriminator = new ParameterEventDiscriminator('identifier', '1234');

        static::assertTrue($discriminator->shouldEnqueue(EventStub::instance(['identifier' => '1234'])));
        static::assertFalse($discriminator->shouldEnqueue(EventStub::instance(['identifier' => true])));
    }
}
