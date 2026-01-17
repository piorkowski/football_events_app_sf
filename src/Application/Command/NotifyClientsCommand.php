<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Event\VO\MatchEventId;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage]
class NotifyClientsCommand implements CommandInterface
{
    public function __construct(
        public MatchEventId $matchEventId,
    ) {
    }
}
