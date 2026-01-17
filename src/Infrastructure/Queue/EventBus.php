<?php

declare(strict_types=1);

namespace App\Infrastructure\Queue;

use App\Application\Event\EventBusInterface;
use App\Domain\Shared\Event\DomainEventInterface;
use App\Infrastructure\Exception\EventBusException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class EventBus implements EventBusInterface
{
    use HandleTrait;

    public function __construct(
        /** @phpstan-ignore-next-line  */
        #[Autowire(service: 'event.bus')]
        private MessageBusInterface $messageBus,
    ) {
    }

    public function dispatch(DomainEventInterface $event): void
    {
        try {
            $this->messageBus->dispatch($event);
        } catch (HandlerFailedException | ExceptionInterface $exception) {
            $previous = $exception->getPrevious() ?? $exception;

            throw new EventBusException(message: $previous->getMessage(), code: $previous->getCode(), previous: $previous);
        }
    }
}
