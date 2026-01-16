<?php

declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\RecordGoalCommand;
use App\Application\MessageBus\CommandHandlerInterface;
use App\Application\MessageBus\EventBusInterface;
use App\Domain\Event\Goal;
use App\Domain\Event\Repository\MatchEventRepositoryInterface;
use App\Domain\Match\VO\MatchId;
use App\Domain\Player\VO\PlayerId;
use App\Domain\Statistics\MatchStatistics;
use App\Domain\Statistics\StatisticsRepositoryInterface;
use App\Domain\Team\VO\TeamId;

final readonly class RecordGoalHandler implements CommandHandlerInterface
{
    public function __construct(
        private MatchEventRepositoryInterface $eventRepository,
        private StatisticsRepositoryInterface $statisticsRepository,
        private EventBusInterface             $eventBus
    ) {}

    public function __invoke(RecordGoalCommand $command): Goal
    {
        $goal = new Goal(
            id: $command->matchEventId,
            matchId: new MatchId($command->eventDTO->matchId),
            teamId: new TeamId($command->eventDTO->teamId),
            scorerId: new PlayerId($command->eventDTO->scorerId),
            minute: $command->eventDTO->minute,
            second: $command->eventDTO->second,
            assistId: $command->eventDTO->assistId ? new PlayerId($command->eventDTO->assistId) : null
        );

        // Save event
        $this->eventRepository->save($goal);

        $matchId = new MatchId($command->eventDTO->matchId);
        $statistics = $this->statisticsRepository->findByMatchId($matchId);

        if ($statistics === null) {
            $statistics = new MatchStatistics($matchId);
        }

        $statistics->incrementGoals(new TeamId($command->eventDTO->teamId));
        $this->statisticsRepository->save($statistics);

        $this->eventBus->publish($goal);

        return $goal;
    }
}
