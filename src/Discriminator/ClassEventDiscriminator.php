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

class ClassEventDiscriminator implements EventDiscriminator
{
    /**
     * @var string
     */
    private $className;

    /**
     * AsyncEventClassDiscriminator constructor.
     *
     * @param string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldEnqueue(Event $event): bool
    {
        return \is_a($event, $this->className);
    }
}
