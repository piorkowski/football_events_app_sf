<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Shared\Event\DomainEventInterface;

interface EventBusInterface
{
    public function dispatch(DomainEventInterface $event): void;
}
