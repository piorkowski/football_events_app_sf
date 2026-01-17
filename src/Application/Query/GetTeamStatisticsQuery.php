<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\UI\DTO\GetStatisticDTO;

final class GetTeamStatisticsQuery implements QueryInterface
{
    public function __construct(public GetStatisticDTO $teamStatisticsDTO)
    {
    }
}
