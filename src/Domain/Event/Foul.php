<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\Event\VO\MatchEventId;
use App\Domain\Match\VO\MatchId;
use App\Domain\Player\VO\PlayerId;
use App\Domain\Team\VO\TeamId;

final class Foul extends MatchEvent
{
    public function __construct(
        public MatchEventId $id,
        public MatchId $matchId,
        public TeamId $teamId,
        public PlayerId $committedBy,
        public ?PlayerId $sufferedBy,
        public int $minute,
        public int $second,
        public ?\DateTimeInterface $timestamp = new \DateTimeImmutable(),
    ) {
        parent::__construct($id, $matchId, $teamId, $minute, $this->second, $timestamp);
    }

    public function type(): EventType
    {
        return EventType::FOUL;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'type' => $this->type()->value,
            'match_id' => $this->matchId->value(),
            'team_id' => $this->teamId->value(),
            'committed_by' => $this->committedBy->value(),
            'suffered_by' => $this->sufferedBy->value(),
            'minute' => $this->minute,
            'second' => $this->second,
            'timestamp' => $this->timestamp->format('Y-m-d H:i:s'),
        ];
    }
}
