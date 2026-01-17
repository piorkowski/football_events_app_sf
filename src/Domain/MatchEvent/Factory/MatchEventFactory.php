<?php

declare(strict_types=1);

namespace App\Domain\MatchEvent\Factory;

use App\Application\Factory\MatchEventFactoryInterface;
use App\Domain\Match\VO\MatchId;
use App\Domain\MatchEvent\Foul;
use App\Domain\MatchEvent\Goal;
use App\Domain\MatchEvent\VO\MatchEventId;
use App\Domain\Player\VO\PlayerId;
use App\Domain\Team\VO\TeamId;

class MatchEventFactory implements MatchEventFactoryInterface
{
    public function createGoal(
        MatchEventId $id,
        MatchId $matchId,
        TeamId $teamId,
        PlayerId $scorerId,
        int $minute,
        int $second,
        ?PlayerId $assistId = null,
    ): Goal {
        return new Goal($id, $matchId, $teamId, $scorerId, $minute, $second, $assistId);
    }

    public function createFoul(
        MatchEventId $id,
        MatchId $matchId,
        TeamId $teamId,
        PlayerId $committedBy,
        ?PlayerId $sufferedBy,
        int $minute,
        int $second,
    ): Foul {
        return new Foul($id, $matchId, $teamId, $committedBy, $sufferedBy, $minute, $second);
    }
}
