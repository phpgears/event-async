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
        $discriminator = new ParameterEventDiscriminator('parameter');
        static::assertTrue($discriminator->shouldEnqueue(EventStub::instance(['parameter' => null])));

        $discriminator = new ParameterEventDiscriminator('unknown');
        static::assertFalse($discriminator->shouldEnqueue(EventStub::instance([])));
    }

    public function testDiscriminateParameterValue(): void
    {
        $discriminator = new ParameterEventDiscriminator('parameter', 'value');

        static::assertTrue($discriminator->shouldEnqueue(EventStub::instance(['parameter' => 'value'])));
        static::assertFalse($discriminator->shouldEnqueue(EventStub::instance(['parameter' => true])));
    }
}
