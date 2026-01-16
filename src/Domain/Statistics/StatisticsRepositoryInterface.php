<?php

declare(strict_types=1);

namespace App\Domain\Statistics;

use App\Domain\Match\VO\MatchId;
use App\Domain\Team\VO\TeamId;

interface StatisticsRepositoryInterface
{
    public function findByMatchId(MatchId $matchId): ?MatchStatistics;

    public function findForTeamByMatchId(TeamId $teamId, MatchId $matchId): ?MatchStatistics;
}
