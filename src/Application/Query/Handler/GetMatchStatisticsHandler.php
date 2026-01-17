<?php

declare(strict_types=1);

namespace App\Application\Query\Handler;

use App\Application\Query\GetMatchStatisticsQuery;
use App\Domain\Match\VO\MatchId;
use App\Domain\Statistics\StatisticsRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetMatchStatisticsHandler
{
    public function __construct(
        private StatisticsRepositoryInterface $statisticsRepository,
    ) {
    }

    public function __invoke(GetMatchStatisticsQuery $query): array
    {
        $matchId = new MatchId($query->matchStatisticsDTO->match_id);
        $statistics = $this->statisticsRepository->findByMatchId($matchId);

        if (null === $statistics) {
            return [
                'match_id' => $query->matchStatisticsDTO->match_id,
                'teams' => [],
            ];
        }

        return $statistics->toArray();
    }
}
