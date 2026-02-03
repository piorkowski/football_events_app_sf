<?php

declare(strict_types=1);

namespace App\Infrastructure\Queue;

use App\Application\Query\QueryBusInterface;
use App\Infrastructure\Exception\QueryBusException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class QueryBus implements QueryBusInterface
{
    use HandleTrait;

    public function __construct(
        #[Autowire(service: 'query.bus.statistics')]
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function ask(object $query): mixed
    {
        try {
            return $this->handle($query);
        } catch (HandlerFailedException $exception) {
            $previous = $exception->getPrevious() ?? $exception;

            throw new QueryBusException(message: $previous->getMessage(), code: $previous->getCode(), previous: $previous);
        }
    }
}
