<?php
declare(strict_types=1);


namespace App\Application\Query;


use App\Domain\Event\VO\MatchEventId;

class GetMatchEventQuery implements QueryInterface
{
    public function __construct(public MatchEventId $matchEventId)
    {
    }
}
