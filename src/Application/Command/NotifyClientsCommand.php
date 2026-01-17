<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\MatchEvent\VO\MatchEventId;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage]
final readonly class NotifyClientsCommand implements CommandInterface
{
    public function __construct(
        public MatchEventId $matchEventId,
    ) {
    }
}
