<?php

declare(strict_types=1);

namespace App\Domain\Shared\Event;

interface DomainEventDispatcherInterface
{
    public function dispatch(DomainEventInterface $event): void;

    public function dispatchAll(array $events): void;
}
