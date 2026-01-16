<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Event\VO\MatchEventId;
use App\UI\DTO\CommitEventDTO;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage]
class RecordGoalCommand implements CommandInterface
{
    public function __construct(
        public MatchEventId $matchEventId,
        public CommitEventDTO $eventDTO
    ) {}
}
