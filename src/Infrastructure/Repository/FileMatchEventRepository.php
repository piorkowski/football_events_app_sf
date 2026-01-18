<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Match\VO\MatchId;
use App\Domain\MatchEvent\EventType;
use App\Domain\MatchEvent\Foul;
use App\Domain\MatchEvent\Goal;
use App\Domain\MatchEvent\MatchEvent;
use App\Domain\MatchEvent\Repository\MatchEventProjectionRepositoryInterface;
use App\Domain\MatchEvent\Repository\MatchEventRepositoryInterface;
use App\Domain\MatchEvent\VO\MatchEventId;
use App\Domain\Player\VO\PlayerId;
use App\Domain\Team\VO\TeamId;
use App\Infrastructure\Exception\InfrastructureException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class FileMatchEventRepository implements MatchEventRepositoryInterface, MatchEventProjectionRepositoryInterface
{
    public function __construct(
        #[Autowire(env: 'EVENT_FILENAME')]
        private string $filePath,
    ) {
        $this->ensureDirectoryExists();
    }

    public function save(MatchEvent $event): void
    {
        $data = $event->toArray();
        $line = json_encode($data, JSON_THROW_ON_ERROR).PHP_EOL;

        file_put_contents($this->filePath, $line, FILE_APPEND | LOCK_EX);
    }

    /**
     * @throws InfrastructureException
     */
    public function findByMatchId(MatchId $matchId): array
    {
        $allEvents = $this->findAll();

        return array_values(array_filter(
            $allEvents,
            static fn (MatchEvent $event) => $event->matchId()->equals($matchId)
        ));
    }

    /**
     * @throws InfrastructureException
     */
    public function findByMatchEventId(MatchEventId $matchEventId): ?MatchEvent
    {
        $allEvents = $this->findAll();

        foreach ($allEvents as $event) {
            if ($event->id()->equals($matchEventId)) {
                return $event;
            }
        }

        return null;
    }

    /**
     * @return MatchEvent[]
     *
     * @throws InfrastructureException
     */
    public function findAll(): array
    {
        if (!file_exists($this->filePath)) {
            return [];
        }

        $content = file_get_contents($this->filePath);
        if (empty(trim($content))) {
            return [];
        }

        $lines = explode(PHP_EOL, trim($content));
        $events = [];

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            $data = json_decode($line, true);
            if (null === $data) {
                continue;
            }

            $events[] = $this->hydrate($data);
        }

        return $events;
    }

    /**
     * @throws InfrastructureException
     */
    private function hydrate(array $data): MatchEvent
    {
        try {
            $eventType = EventType::from($data['type']);

            return match ($eventType) {
                EventType::GOAL => $this->hydrateGoal($data),
                EventType::FOUL => $this->hydrateFoul($data),
            };
        } catch (\Throwable $e) {
            throw new InfrastructureException('Unable to hydrate event: '.$e->getMessage(), 0, $e);
        }
    }

    private function hydrateGoal(array $data): Goal
    {
        return new Goal(
            id: new MatchEventId($data['id']),
            matchId: new MatchId($data['match_id']),
            teamId: new TeamId($data['team_id']),
            scorerId: new PlayerId($data['scorer_id']),
            minute: $data['minute'],
            second: $data['second'],
            assistId: isset($data['assist_id']) ? new PlayerId($data['assist_id']) : null,
            timestamp: new \DateTimeImmutable($data['timestamp']),
        );
    }

    private function hydrateFoul(array $data): Foul
    {
        return new Foul(
            id: new MatchEventId($data['id']),
            matchId: new MatchId($data['match_id']),
            teamId: new TeamId($data['team_id']),
            committedBy: new PlayerId($data['committed_by']),
            sufferedBy: isset($data['suffered_by']) ? new PlayerId($data['suffered_by']) : null,
            minute: $data['minute'],
            second: $data['second'],
            timestamp: new \DateTimeImmutable($data['timestamp']),
        );
    }

    private function ensureDirectoryExists(): void
    {
        $directory = dirname($this->filePath);

        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true) && !is_dir($directory)) {
                throw new InfrastructureException("Failed to create directory: {$directory}");
            }
        }

        if (!file_exists($this->filePath)) {
            if (false === touch($this->filePath)) {
                throw new InfrastructureException("Failed to create file: {$this->filePath}");
            }
            chmod($this->filePath, 0644);
        }

        if (!is_writable($this->filePath)) {
            throw new InfrastructureException("File is not writable: {$this->filePath}");
        }
    }
}
