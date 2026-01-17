<?php

declare(strict_types=1);

namespace App\Infrastructure\Queue;

use App\Application\Command\CommandBusInterface;
use App\Application\Command\CommandInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class CommandBus implements CommandBusInterface
{
    public function __construct(
        #[Autowire(service: 'command.bus.events')]
        private MessageBusInterface $commandBus,
    ) {
    }

    public function dispatch(CommandInterface $command): void
    {
        try {
            $this->commandBus->dispatch($command);
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious() ?? $exception;
        } catch (ExceptionInterface $e) {
            throw $e->getPrevious() ?? $e;
        }
    }
}
