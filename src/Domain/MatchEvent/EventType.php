<?php

declare(strict_types=1);

namespace App\Domain\MatchEvent;

enum EventType: string
{
    case GOAL = 'goal';
    case FOUL = 'foul';
}
