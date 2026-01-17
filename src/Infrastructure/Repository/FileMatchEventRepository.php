<?php

namespace App\Infrastructure\Repository;

use App\Domain\Event\EventType;
use App\Domain\Event\Foul;
use App\Domain\Event\Goal;
use App\Domain\Event\MatchEvent;
use App\Domain\Event\Repository\MatchEventProjectionRepositoryInterface;
use App\Domain\Event\Repository\MatchEventRepositoryInterface;
use App\Domain\Event\VO\MatchEventId;
use App\Domain\Match\VO\MatchId;
use App\Domain\Player\VO\PlayerId;
use App\Domain\Team\VO\TeamId;
use App\Infrastructure\Exception\InfrastructureException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class FileMatchEventRepository implements MatchEventRepositoryInterface, MatchEventProjectionRepositoryInterface
{
    public function __construct(
        #[Autowire(env: 'EVENT_FILENAME')]
        private string $filePath)
    {
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

        return array_filter($allEvents, static function (MatchEvent $event) use ($matchId) {
            return $event->matchId()->equals($matchId);
        });
    }

    public function findByMatchEventId(MatchEventId $id): MatchEvent
    {
        $allEvents = $this->findAll();

        array_filter($allEvents, static function (MatchEvent $event) {
            return $event;
        });
    }

    /**
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

    private function hydrate(array $data): MatchEvent
    {
        $eventType = EventType::from($data['type']);
        try {
            return match ($eventType) {
                EventType::GOAL => new Goal(
                    id: new MatchEventId($data['id']),
                    matchId: new MatchId($data['match_id']),
                    teamId: new TeamId($data['team_id']),
                    scorerId: new PlayerId($data['scorer_id']),
                    minute: $data['minute'],
                    second: $data['second'],
                    assistId: isset($data['assist_id']) ? new PlayerId($data['assist_id']) : null,
                    timestamp: new \DateTimeImmutable($data['timestamp'])
                ),
                EventType::FOUL => new Foul(
                    id: new MatchEventId($data['id']),
                    matchId: new MatchId($data['match_id']),
                    teamId: new TeamId($data['team_id']),
                    committedBy: new PlayerId($data['committed_by']),
                    sufferedBy: new PlayerId($data['suffered_by']),
                    minute: $data['minute'],
                    second: $data['second'],
                    timestamp: new \DateTimeImmutable($data['timestamp'])
                ),
            };
        } catch (\Throwable $e) {
            throw new InfrastructureException('Unable to hydrate event', 0, $e);
        }
    }

    private function ensureDirectoryExists(): void
    {
        $directory = dirname($this->filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0o777, true);
        }
    }
}
