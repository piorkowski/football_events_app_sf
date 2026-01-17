<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Application\Command\CommandBusInterface;
use App\Application\Command\NotifyClientsCommand;
use App\Domain\MatchEvent\Event\GoalScoredEvent;
use App\Domain\Statistics\MatchStatistics;
use App\Domain\Statistics\StatisticsProjectionRepositoryInterface;
use App\Domain\Statistics\StatisticsRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateStatisticsOnGoalHandler
{
    public function __construct(
        private StatisticsProjectionRepositoryInterface $statisticsProjectionRepository,
        private StatisticsRepositoryInterface $statisticsRepository,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(GoalScoredEvent $event): void
    {
        $statistics = $this->statisticsRepository->findByMatchId($event->matchId)
            ?? new MatchStatistics($event->matchId);

        $statistics->incrementGoals($event->teamId);

        $this->statisticsProjectionRepository->save($statistics);
        $this->commandBus->dispatch(new NotifyClientsCommand($event->eventId));
    }
}
