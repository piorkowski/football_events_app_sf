<?php

declare(strict_types=1);

namespace App\Domain\MatchEvent\Repository;

use App\Domain\Match\VO\MatchId;
use App\Domain\MatchEvent\MatchEvent;
use App\Domain\MatchEvent\VO\MatchEventId;

interface MatchEventRepositoryInterface
{
    public function findByMatchId(MatchId $matchId): array;

    public function findByMatchEventId(MatchEventId $matchId): MatchEvent;

    public function findAll(): array;
}
