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

use Gears\DTO\Exception\InvalidParameterException;
use Gears\Event\Async\Exception\QueuedEventException;
use Gears\Event\Event;

final class QueuedEvent implements Event, \Serializable
{
    /**
     * @var Event
     */
    private $wrappedEvent;

    /**
     * @var \DateTimeImmutable
     */
    private $createdAt;

    /**
     * ReceivedEvent constructor.
     *
     * @param Event $originalEvent
     */
    public function __construct(Event $originalEvent)
    {
        $this->wrappedEvent = $originalEvent;
        $this->createdAt = new \DateTimeImmutable('now');
    }

    /**
     * Get wrapped event.
     *
     * @return Event
     */
    public function getWrappedEvent(): Event
    {
        return $this->wrappedEvent;
    }

    /**
     * {@inheritdoc}
     *
     * @throws QueuedEventException
     */
    public function getEventType(): string
    {
        throw new QueuedEventException(\sprintf('Method %s should not be called ', __METHOD__));
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidParameterException
     *
     * @return mixed
     */
    public function get(string $parameter)
    {
        if ($parameter !== 'wrappedEvent') {
            throw new InvalidParameterException(\sprintf(
                'Payload parameter "%s" on "%s" does not exist',
                $parameter,
                static::class
            ));
        }

        return $this->wrappedEvent;
    }

    /**
     * {@inheritdoc}
     */
    public function getPayload(): array
    {
        return ['wrappedEvent' => $this->wrappedEvent];
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return ['wrappedEvent' => $this->wrappedEvent];
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     *
     * @throws QueuedEventException
     */
    public function withAddedMetadata(array $metadata): void
    {
        throw new QueuedEventException(\sprintf('Method %s should not be called ', __METHOD__));
    }

    /**
     * {@inheritdoc}
     *
     * @throws QueuedEventException
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public static function reconstitute(iterable $payload, \DateTimeImmutable $createdAt, array $attributes = [])
    {
        $payload = \is_array($payload) ? $payload : \iterator_to_array($payload);

        $eventClass = static::class;

        return new $eventClass($payload['wrappedEvent']);
    }

    /**
     * @return array<string, mixed>
     */
    public function __serialize(): array
    {
        return ['wrappedEvent' => \serialize($this->wrappedEvent)];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __unserialize(array $data): void
    {
        $this->wrappedEvent = \unserialize($data['wrappedEvent']);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return \serialize(['wrappedEvent' => $this->wrappedEvent]);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $serialized
     */
    public function unserialize($serialized): void
    {
        $data = \unserialize($serialized);

        $this->wrappedEvent = $data['wrappedEvent'];
    }
}
