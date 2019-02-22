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

namespace Gears\Event\Async\Discriminator;

use Gears\Event\Event;

final class ParameterEventDiscriminator implements EventDiscriminator
{
    /**
     * Event parameter name.
     *
     * @var string
     */
    private $parameter;

    /**
     * Expected event parameter value.
     *
     * @var mixed|null
     */
    private $value;

    /**
     * ParameterEventDiscriminator constructor.
     *
     * @param string     $parameter
     * @param mixed|null $value
     */
    public function __construct(string $parameter, $value = null)
    {
        $this->parameter = $parameter;
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldEnqueue(Event $event): bool
    {
        return $event->has($this->parameter)
            && ($this->value === null || $event->get($this->parameter) === $this->value);
    }
}
