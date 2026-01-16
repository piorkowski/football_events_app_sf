<?php

declare(strict_types=1);

namespace App\Domain\Statistics;

interface StatisticsProjectionRepositoryInterface
{
    public function save(MatchStatistics $statistics): void;
}
