<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\MatchEvent\MatchEvent;

interface EventCreatorInterface
{
    public function __construct();

    public function create(): MatchEvent;
}
