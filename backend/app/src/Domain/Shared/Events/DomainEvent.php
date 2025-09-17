<?php

declare(strict_types=1);

namespace App\src\Domain\Shared\Events;

use DateTimeImmutable;

interface DomainEvent
{
    /**
     * Get the date and time when the event occurred
     */
    public function occurredOn(): DateTimeImmutable;

    /**
     * Get event data as array
     */
    public function toArray(): array;
}