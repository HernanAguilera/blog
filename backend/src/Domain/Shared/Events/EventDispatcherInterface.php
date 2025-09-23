<?php

declare(strict_types=1);

namespace Blog\Domain\Shared\Events;

interface EventDispatcherInterface
{
    /**
     * Dispatch an event
     */
    public function dispatch(object $event): void;
}