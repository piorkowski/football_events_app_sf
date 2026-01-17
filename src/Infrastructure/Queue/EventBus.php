<?php

declare(strict_types=1);

namespace App\Infrastructure\Queue;

use App\Application\Event\EventBusInterface;
use App\Infrastructure\Exception\QueryBusException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
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

    public function query(object $query): mixed
    {
        try {
            return $this->handle($query);
        } catch (HandlerFailedException $exception) {
            $previous = $exception->getPrevious() ?? $exception;

            throw new QueryBusException(message: $previous->getMessage(), code: $previous->getCode(), previous: $previous);
        }
    }
}
