<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\Event\VO\MatchEventId;
use App\Domain\Match\VO\MatchId;
use App\Domain\Shared\AggregateRoot;
use App\Domain\Team\VO\TeamId;

abstract class MatchEvent extends AggregateRoot
{
    public function __construct(
        protected MatchEventId $id,
        protected MatchId $matchId,
        protected TeamId $teamId,
        protected int $minute,
        protected int $second,
        protected ?\DateTimeInterface $timestamp = null,
    ) {
    }

    public function id(): MatchEventId
    {
        return $this->id;
    }

    abstract public function type(): EventType;

    public function matchId(): MatchId
    {
        return $this->matchId;
    }

    public function teamId(): TeamId
    {
        return $this->teamId;
    }

    public function minute(): int
    {
        return $this->minute;
    }

    public function second(): int
    {
        return $this->second;
    }

    abstract public function toArray(): array;
}
