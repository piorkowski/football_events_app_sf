<?php

declare(strict_types=1);

namespace App\UI\Response;

final readonly class TeamStatisticsResponse
{
    public function __construct(
        public string $teamId,
        public string $matchId,
        public array $statistics,
    ) {
    }

    public function __invoke(): string
    {
        return json_encode([
            'match_id' => $this->matchId,
            'team_id' => $this->teamId,
            'statistics' => $this->statistics,
        ], JSON_THROW_ON_ERROR);
    }
}
