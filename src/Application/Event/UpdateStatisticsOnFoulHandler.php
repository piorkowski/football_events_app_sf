<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Application\Command\CommandBusInterface;
use App\Application\Command\NotifyClientsCommand;
use App\Domain\MatchEvent\Event\FoulCommittedEvent;
use App\Domain\Statistics\MatchStatistics;
use App\Domain\Statistics\StatisticsProjectionRepositoryInterface;
use App\Domain\Statistics\StatisticsRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateStatisticsOnFoulHandler
{
    public function __construct(
        private StatisticsProjectionRepositoryInterface $statisticsProjectionRepository,
        private StatisticsRepositoryInterface $statisticsRepository,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(FoulCommittedEvent $event): void
    {
        $statistics = $this->statisticsRepository->findByMatchId($event->matchId)
            ?? new MatchStatistics($event->matchId);

        $statistics->incrementFouls($event->teamId);

        $this->statisticsProjectionRepository->save($statistics);
        $this->commandBus->dispatch(new NotifyClientsCommand($event->eventId));
    }
}
