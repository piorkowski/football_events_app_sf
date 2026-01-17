<?php

declare(strict_types=1);

namespace App\Domain\MatchEvent\Event;

use App\Domain\Match\VO\MatchId;
use App\Domain\MatchEvent\VO\MatchEventId;
use App\Domain\Shared\Event\DomainEventInterface;
use App\Domain\Team\VO\TeamId;

final readonly class GoalScoredEvent implements DomainEventInterface
{
    public function __construct(
        public MatchEventId $eventId,
        public MatchId $matchId,
        public TeamId $teamId,
        public \DateTimeImmutable $occurredAt = new \DateTimeImmutable(),
    ) {
    }
}
