<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\MatchEvent\VO\MatchEventId;
use App\UI\DTO\CommitEventDTO;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage]
final readonly class RecordFoulCommand implements CommandInterface
{
    public function __construct(
        public MatchEventId $matchEventId,
        public CommitEventDTO $eventDTO,
    ) {
    }
}
