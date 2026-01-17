<?php

declare(strict_types=1);

namespace App\Domain\Event\Repository;

use App\Domain\Event\MatchEvent;
use App\Domain\Event\VO\MatchEventId;
use App\Domain\Match\VO\MatchId;

interface MatchEventRepositoryInterface
{
    public function findByMatchId(MatchId $matchId): array;

    public function findByMatchEventId(MatchEventId $matchId): MatchEvent;

    public function findAll(): array;
}
