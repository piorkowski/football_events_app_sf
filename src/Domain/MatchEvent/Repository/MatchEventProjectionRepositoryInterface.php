<?php

declare(strict_types=1);

namespace App\Domain\MatchEvent\Repository;

use App\Domain\MatchEvent\MatchEvent;

interface MatchEventProjectionRepositoryInterface
{
    public function save(MatchEvent $event): void;
}
