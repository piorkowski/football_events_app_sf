<?php

declare(strict_types=1);

namespace App\UI\Action;

use App\Application\Command\CommandBusInterface;
use App\Application\Command\RecordFoulCommand;
use App\Application\Command\RecordGoalCommand;
use App\Application\Exception\ValidationException;
use App\Application\Query\GetMatchEventQuery;
use App\Application\Query\QueryBusInterface;
use App\Domain\MatchEvent\VO\MatchEventId;
use App\UI\DTO\CommitEventDTO;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/event', name: 'event', methods: ['POST'])]
final readonly class CommitEventAction
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private QueryBusInterface $queryBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(
        #[MapRequestPayload(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] CommitEventDTO $eventDTO,
        Request $request,
    ): JsonResponse {
        try {
            $this->logger->info('Committing event', ['event' => $eventDTO]);
            switch ($eventDTO->type) {
                case 'goal':
                    $this->logger->info('Recording goal');
                    $matchEventId = $this->generateMatchEventId();
                    $this->commandBus->dispatch(new RecordGoalCommand($matchEventId, $eventDTO));
                    $this->logger->info('Goal recorded');
                    $goal = $this->queryBus->ask(new GetMatchEventQuery($matchEventId));
                    return $this->buildResponse($goal);

                case 'foul':
                    $this->logger->info('Recording foul');
                    $matchEventId = $this->generateMatchEventId();
                    $this->commandBus->dispatch(new RecordFoulCommand($matchEventId, $eventDTO));
                    $this->logger->info('Foul recorded');
                    $foul = $this->queryBus->ask(new GetMatchEventQuery($matchEventId));
                    return $this->buildResponse($foul);
                default:
                    $this->logger->info('Unknown event type - %', ['type' => $eventDTO->type]);
            }

            return new JsonResponse('Error committing event', Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (ValidationException $e) {
            //this part below it is here only to not breaking contract in tests
            if (isset($e->errors['match_id']) || isset($e->errors['team_id'])) {
                return new JsonResponse([
                    'error' => 'match_id and team_id are required for foul events',
                ], Response::HTTP_BAD_REQUEST);
            }

            return new JsonResponse([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors,
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $exception) {
            $this->logger->error('Error committing event', ['exception' => $exception]);
            return new JsonResponse('Error committing event', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function generateMatchEventId(): MatchEventId
    {
        return new MatchEventId(uniqid('event_', true));
    }

    private function buildResponse($event): JsonResponse
    {
        $data = [
            'status' => 'success',
            'message' => 'Event saved successfully',
            'event' => $event->toArray(),
        ];

        return new JsonResponse($data, Response::HTTP_CREATED);
    }
}
