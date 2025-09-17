<?php

declare(strict_types=1);

namespace App\src\Domain\Shared\Events;

interface EventDispatcherInterface
{
    /**
     * Dispatch an event
     */
    public function dispatch(object $event): void;
}