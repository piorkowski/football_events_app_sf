<?php

declare(strict_types=1);

namespace App\UI\Action;

use App\Application\Command\CommandBusInterface;
use App\UI\DTO\CommitEventDTO;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/statistics', name: 'statistics', methods: ['GET'])]
final readonly class GetStatisticsAction
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(
        #[MapRequestPayload] CommitEventDTO $eventDTO,
        Request                             $request,
    ): JsonResponse {
        try {
            $this->commandBus->dispatch(new CreateOrderCommand($createOrderDTO, $request->getClientIp()));

            return new JsonResponse('Order created!', Response::HTTP_CREATED);
        } catch (CannotCreateOrderException $exception) {
            return new JsonResponse('Order cannot be created!', Response::HTTP_BAD_REQUEST);
        }
    }
}
