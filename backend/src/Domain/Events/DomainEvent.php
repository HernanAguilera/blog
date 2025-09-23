<?php

declare(strict_types=1);

namespace Blog\Domain\Events;

use DateTimeImmutable;

abstract class DomainEvent
{
    private readonly DateTimeImmutable $occurredOn;
    private readonly string $eventId;

    public function __construct()
    {
        $this->occurredOn = new DateTimeImmutable();
        $this->eventId = uniqid('event_', true);
    }

    abstract public function eventName(): string;

    abstract public function toPrimitives(): array;

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }

    public function eventId(): string
    {
        return $this->eventId;
    }

    public function toArray(): array
    {
        return [
            'event_id' => $this->eventId,
            'event_name' => $this->eventName(),
            'occurred_on' => $this->occurredOn->format('Y-m-d H:i:s'),
            'data' => $this->toPrimitives()
        ];
    }
}