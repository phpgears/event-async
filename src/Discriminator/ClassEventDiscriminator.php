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

final class ClassEventDiscriminator implements EventDiscriminator
{
    /**
     * @var string
     */
    private $class;

    /**
     * ClassEventDiscriminator constructor.
     *
     * @param string $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldEnqueue(Event $event): bool
    {
        return \is_a($event, $this->class);
    }
}
