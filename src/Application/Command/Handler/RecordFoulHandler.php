<?php

declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\RecordFoulCommand;
use App\Application\Event\EventBusInterface;
use App\Domain\Event\Foul;
use App\Domain\Event\Repository\MatchEventRepositoryInterface;
use App\Domain\Match\VO\MatchId;
use App\Domain\Player\VO\PlayerId;
use App\Domain\Statistics\MatchStatistics;
use App\Domain\Statistics\StatisticsRepositoryInterface;
use App\Domain\Team\VO\TeamId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RecordFoulHandler
{
    public function __construct(
        private MatchEventRepositoryInterface $eventRepository,
        private StatisticsRepositoryInterface $statisticsRepository,
        private EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(RecordFoulCommand $command): Foul
    {
        $foul = new Foul(
            id: $command->matchEventId,
            matchId: new MatchId($command->eventDTO->matchId),
            teamId: new TeamId($command->eventDTO->teamId),
            committedBy: new PlayerId($command->eventDTO->committedBy),
            sufferedBy: new PlayerId($command->eventDTO->sufferedBy),
            minute: $command->eventDTO->minute,
            second: $command->eventDTO->second
        );

        $this->eventRepository->save($foul);

        $matchId = new MatchId($command->eventDTO->matchId);
        $statistics = $this->statisticsRepository->findByMatchId($matchId);

        if (null === $statistics) {
            $statistics = new MatchStatistics($matchId);
        }

        $statistics->incrementFouls(new TeamId($command->eventDTO->teamId));
        $this->statisticsRepository->save($statistics);

        $this->eventBus->publish($foul);

        return $foul;
    }
}
