<?php

declare(strict_types=1);

namespace App\Application\Query\Handler;

use App\Application\MessageBus\QueryHandlerInterface;
use App\Application\Query\GetTeamStatisticsQuery;
use App\Domain\Match\VO\MatchId;
use App\Domain\Statistics\StatisticsRepositoryInterface;
use App\Domain\Team\VO\TeamId;

readonly class GetTeamStatisticsQueryHandler implements QueryHandlerInterface
{
    public function __construct(private StatisticsRepositoryInterface $statisticsRepository) {}

    public function handle(GetTeamStatisticsQuery $query): array
    {
        $teamId = new TeamId($query->teamStatisticsDTO->teamId);
        $matchId = new MatchId($query->teamStatisticsDTO->matchId);
        $statistics = $this->statisticsRepository->findForTeamByMatchId($teamId, $matchId);

        if ($statistics === null) {
            return [
                'match_id' => $query->teamStatisticsDTO->matchId,
                'teams' => [],
            ];
        }

        return $statistics->toArray();
    }
}
