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

namespace Gears\Event\Async\Tests\Stub;

use Gears\Event\AbstractEvent;
use Gears\Event\Time\FixedTimeProvider;

/**
 * Event stub class.
 */
class EventStub extends AbstractEvent
{
    /**
     * @var string
     */
    private $parameter;

    /**
     * Instantiate event.
     *
     * @param mixed[] $parameters
     *
     * @return self
     */
    public static function instance(array $parameters = []): self
    {
        return self::occurred(
            $parameters,
            new FixedTimeProvider(\DateTimeImmutable::createFromFormat(\DateTime::ATOM, '2020-01-01T00:00:00Z'))
        );
    }
}
