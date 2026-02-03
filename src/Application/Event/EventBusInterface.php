<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Shared\Event\DomainEventInterface;
use App\Infrastructure\Exception\EventBusException;

interface EventBusInterface
{
    /**
     * @throws EventBusException
     */
    public function dispatch(DomainEventInterface $event): void;
}
