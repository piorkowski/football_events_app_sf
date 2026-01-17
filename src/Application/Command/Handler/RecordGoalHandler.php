<?php

declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\RecordGoalCommand;
use App\Application\Exception\ApplicationException;
use App\Application\Exception\ValidationException;
use App\Application\Factory\MatchEventFactoryInterface;
use App\Application\Validator\MatchEventValidatorInterface;
use App\Domain\Match\VO\MatchId;
use App\Domain\MatchEvent\Goal;
use App\Domain\MatchEvent\Repository\MatchEventRepositoryInterface;
use App\Domain\Player\VO\PlayerId;
use App\Domain\Shared\Event\DomainEventDispatcherInterface;
use App\Domain\Team\VO\TeamId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RecordGoalHandler
{
    public function __construct(
        private MatchEventValidatorInterface $validator,
        private MatchEventFactoryInterface $factory,
        private MatchEventRepositoryInterface $eventRepository,
        private DomainEventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(RecordGoalCommand $command): Goal
    {
        try {
            $this->validator->validate($command->eventDTO->type, $command->eventDTO->data);

            $goal = $this->factory->createGoal(
                $command->matchEventId,
                new MatchId($command->eventDTO->data['match_id']),
                new TeamId($command->eventDTO->data['team_id']),
                new PlayerId($command->eventDTO->data['scorer_id'] ?? $command->eventDTO->data['player']),
                $command->eventDTO->data['minute'],
                $command->eventDTO->data['second'],
                isset($command->eventDTO->data['assist_id'])
                    ? new PlayerId($command->eventDTO->data['assist_id'])
                    : null
            );

            $this->eventRepository->save($goal);
            $this->eventDispatcher->dispatchAll($goal->pullDomainEvents());

            return $goal;
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new ApplicationException(previous: $e);
        }
    }
}
