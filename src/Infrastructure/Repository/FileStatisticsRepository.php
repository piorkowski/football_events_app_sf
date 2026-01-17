<?php

namespace App\Infrastructure\Repository;

use App\Domain\Match\VO\MatchId;
use App\Domain\Statistics\MatchStatistics;
use App\Domain\Statistics\StatisticsProjectionRepositoryInterface;
use App\Domain\Statistics\StatisticsRepositoryInterface;
use App\Domain\Team\VO\TeamId;
use App\Infrastructure\Exception\InfrastructureException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class FileStatisticsRepository implements StatisticsRepositoryInterface, StatisticsProjectionRepositoryInterface
{
    public function __construct(
        #[Autowire(env: 'STATISTICS_FILENAME')]
        private string $filePath,
    ) {
        $this->ensureFileExists();
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

    public function findForTeamByMatchId(TeamId $teamId, MatchId $matchId): ?MatchStatistics
    {
        $allStats = $this->loadAll();

        if (!isset($allStats[$matchId->value()])) {
            return null;
        }

        $statistics = new MatchStatistics($matchId, $allStats[$matchId->value()][$teamId->value()], $teamId);


        return $statistics;
    }

    private function ensureFileExists(): void
    {
        $directory = dirname($this->filePath);

        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true) && !is_dir($directory)) {
                throw new InfrastructureException("Failed to create directory: {$directory}");
            }
        }

        if (!file_exists($this->filePath)) {
            if (file_put_contents($this->filePath, '{}') === false) {
                throw new InfrastructureException("Failed to create file: {$this->filePath}");
            }
            chmod($this->filePath, 0644);
        }

        if (!is_writable($this->filePath)) {
            throw new InfrastructureException("File is not writable: {$this->filePath}");
        }
    }
}
