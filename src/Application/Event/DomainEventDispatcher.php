<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Shared\Event\DomainEventDispatcherInterface;
use App\Domain\Shared\Event\DomainEventInterface;

readonly class DomainEventDispatcher implements DomainEventDispatcherInterface
{
    public function __construct(
        private EventBusInterface $eventBus,
    ) {
    }

    public function dispatch(DomainEventInterface $event): void
    {
        $this->eventBus->dispatch($event);
    }

    public function dispatchAll(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }
}
