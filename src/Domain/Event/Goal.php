<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\Event\VO\MatchEventId;
use App\Domain\Match\VO\MatchId;
use App\Domain\Player\VO\PlayerId;
use App\Domain\Team\VO\TeamId;

final class Goal extends MatchEvent
{
    public function __construct(
        public MatchEventId $id,
        public MatchId $matchId,
        public TeamId $teamId,
        public PlayerId $scorerId,
        public int $minute,
        public int $second,
        public ?PlayerId $assistId = null,
        public ?\DateTimeInterface $timestamp = new \DateTimeImmutable(),
    ) {
        parent::__construct($id, $matchId, $teamId, $minute, $second, $timestamp);
    }

    public function type(): EventType
    {
        return EventType::GOAL;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'type' => $this->type()->value,
            'match_id' => $this->matchId->value(),
            'team_id' => $this->teamId->value(),
            'scorer_id' => $this->scorerId->value(),
            'assist_id' => $this->assistId?->value(),
            'minute' => $this->minute,
            'second' => $this->second,
            'timestamp' => $this->timestamp->format('Y-m-d H:i:s'),
        ];
    }
}
