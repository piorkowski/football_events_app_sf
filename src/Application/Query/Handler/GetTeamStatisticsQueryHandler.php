<?php

declare(strict_types=1);

namespace App\Application\Query\Handler;

use App\Application\Query\GetTeamStatisticsQuery;
use App\Domain\Match\VO\MatchId;
use App\Domain\Statistics\StatisticsRepositoryInterface;
use App\Domain\Team\VO\TeamId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class GetTeamStatisticsQueryHandler
{
    public function __construct(private StatisticsRepositoryInterface $statisticsRepository)
    {
    }

    public function __invoke(GetTeamStatisticsQuery $query): array
    {
        $teamId = new TeamId($query->teamStatisticsDTO->teamId);
        $matchId = new MatchId($query->teamStatisticsDTO->matchId);
        $statistics = $this->statisticsRepository->findForTeamByMatchId($teamId, $matchId);

        if (null === $statistics) {
            return [
                'match_id' => $query->teamStatisticsDTO->matchId,
                'teams' => [],
            ];
        }

        return $statistics->toArray();
    }
}
