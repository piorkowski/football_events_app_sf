<?php

declare(strict_types=1);

namespace App\UI\Action;

use App\Application\Query\GetMatchStatisticsQuery;
use App\Application\Query\GetTeamStatisticsQuery;
use App\Application\Query\QueryBusInterface;
use App\UI\DTO\GetStatisticDTO;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/statistics', name: 'statistics', methods: ['GET'])]
final readonly class GetStatisticsAction
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(
        #[MapQueryString] GetStatisticDTO $dto,
    ): JsonResponse {
        try {
            $this->logger->info('Getting statistics', [
                'match_id' => $dto->match_id,
                'team_id' => $dto->team_id,
            ]);

            if ($dto->team_id !== null) {
                $statistics = $this->queryBus->ask(new GetTeamStatisticsQuery($dto));
            } else {
                $statistics = $this->queryBus->ask(new GetMatchStatisticsQuery($dto));
            }

            return new JsonResponse([
                'status' => 'success',
                'data' => $statistics,
            ]);
        } catch (\Exception $exception) {
            $this->logger->error('Error getting statistics', ['exception' => $exception->getMessage()]);
            dd($exception);
            return new JsonResponse(
                ['status' => 'error', 'message' => 'Error getting statistics'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
