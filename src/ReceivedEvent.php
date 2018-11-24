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

namespace Gears\Event\Async;

use Gears\Event\Async\Exception\ReceivedEventException;
use Gears\Event\Event;

final class ReceivedEvent implements Event
{
    /**
     * @var Event
     */
    private $originalEvent;

    /**
     * ReceivedEvent constructor.
     *
     * @param Event $originalEvent
     */
    public function __construct(Event $originalEvent)
    {
        $this->originalEvent = $originalEvent;
    }

    /**
     * Get original event.
     *
     * @return Event
     */
    public function getOriginalEvent(): Event
    {
        return $this->originalEvent;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ReceivedEventException
     */
    public function has(string $parameter): bool
    {
        throw new ReceivedEventException(\sprintf('Method %s should not be called ', __METHOD__));
    }

    /**
     * {@inheritdoc}
     *
     * @throws ReceivedEventException
     *
     * @return mixed
     */
    public function get(string $parameter)
    {
        throw new ReceivedEventException(\sprintf('Method %s should not be called ', __METHOD__));
    }

    /**
     * {@inheritdoc}
     *
     * @throws ReceivedEventException
     *
     * @return array<string, mixed>
     */
    public function getPayload(): array
    {
        throw new ReceivedEventException(\sprintf('Method %s should not be called ', __METHOD__));
    }

    /**
     * {@inheritdoc}
     *
     * @throws ReceivedEventException
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        throw new ReceivedEventException(\sprintf('Method %s should not be called ', __METHOD__));
    }

    /**
     * {@inheritdoc}
     *
     * @throws ReceivedEventException
     */
    public static function reconstitute(array $payload, array $attributes = []): void
    {
        throw new ReceivedEventException(\sprintf('Method %s should not be called ', __METHOD__));
    }
}
