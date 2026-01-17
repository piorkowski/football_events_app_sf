<?php

namespace App\Infrastructure\Repository;

use App\Domain\Match\VO\MatchId;
use App\Domain\Statistics\MatchStatistics;
use App\Domain\Statistics\StatisticsProjectionRepositoryInterface;
use App\Domain\Statistics\StatisticsRepositoryInterface;
use App\Domain\Team\VO\TeamId;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class FileStatisticsRepository implements StatisticsRepositoryInterface, StatisticsProjectionRepositoryInterface
{
    public function __construct(
        #[Autowire(env: 'STATISTICS_FILENAME')]
        private readonly string $filePath,
    ) {
        $this->ensureDirectoryExists();
    }

    public function save(MatchStatistics $statistics): void
    {
        $allStats = $this->loadAll();
        $allStats[$statistics->matchId()->value()] = $statistics->getAllStats();

        file_put_contents(
            $this->filePath,
            json_encode($allStats, JSON_PRETTY_PRINT),
            LOCK_EX
        );
    }

    public function findByMatchId(MatchId $matchId): ?MatchStatistics
    {
        $allStats = $this->loadAll();

        if (!isset($allStats[$matchId->value()])) {
            return null;
        }

        $statistics = new MatchStatistics($matchId);

        foreach ($allStats[$matchId->value()] as $teamIdValue => $stats) {
            $teamId = new TeamId($teamIdValue);

            for ($i = 0; $i < ($stats['goals'] ?? 0); ++$i) {
                $statistics->incrementGoals($teamId);
            }

            for ($i = 0; $i < ($stats['fouls'] ?? 0); ++$i) {
                $statistics->incrementFouls($teamId);
            }
        }

        return $statistics;
    }

    private function loadAll(): array
    {
        if (!file_exists($this->filePath)) {
            return [];
        }

        $content = file_get_contents($this->filePath);

        return json_decode($content, true) ?? [];
    }

    private function ensureDirectoryExists(): void
    {
        $directory = dirname($this->filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0o777, true);
        }
    }

    public function findForTeamByMatchId(TeamId $teamId, MatchId $matchId): ?MatchStatistics
    {
        $allStats = $this->loadAll();

        if (!isset($allStats[$matchId->value()])) {
            return null;
        }

        $statistics = new MatchStatistics($matchId);

        foreach ($allStats[$matchId->value()][$teamId->value()] as $stats) {
            for ($i = 0; $i < ($stats['goals'] ?? 0); ++$i) {
                $statistics->incrementGoals($teamId);
            }

            for ($i = 0; $i < ($stats['fouls'] ?? 0); ++$i) {
                $statistics->incrementFouls($teamId);
            }
        }

        return $statistics;
    }
}
