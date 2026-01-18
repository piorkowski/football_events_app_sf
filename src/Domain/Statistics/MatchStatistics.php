<?php

declare(strict_types=1);

namespace App\Domain\Statistics;

use App\Domain\Match\VO\MatchId;
use App\Domain\Team\VO\TeamId;

final class MatchStatistics
{
    public function __construct(
        private readonly MatchId $matchId,
        private array $teamStats = [],
        private readonly ?TeamId $teamId = null,
    ) {
    }

    public function incrementGoals(TeamId $teamId): void
    {
        $this->increment($teamId, 'goals');
    }

    public function incrementFouls(TeamId $teamId): void
    {
        $this->increment($teamId, 'fouls');
    }

    private function increment(TeamId $teamId, string $statType): void
    {
        $teamIdValue = $teamId->value();

        if (!isset($this->teamStats[$teamIdValue])) {
            $this->teamStats[$teamIdValue] = [
                'goals' => 0,
                'fouls' => 0,
            ];
        }

        ++$this->teamStats[$teamIdValue][$statType];
    }

    public function getTeamStats(TeamId $teamId): array
    {
        return $this->teamStats[$teamId->value()] ?? [
            'goals' => 0,
            'fouls' => 0,
        ];
    }

    public function getAllStats(): array
    {
        return $this->teamStats;
    }

    public function matchId(): MatchId
    {
        return $this->matchId;
    }

    public function toArray(): array
    {
        if ($this->teamId) {
            return [
                'match_id' => $this->matchId->value(),
                'team_id' => $this->teamId->value(),
                'statistics' => $this->teamStats,
            ];
        }

        return [
            'match_id' => $this->matchId->value(),
            'statistics' => $this->teamStats,
        ];
    }
}
