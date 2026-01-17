<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Event\MatchEvent;

interface EventCreatorInterface
{
    public function __construct();

    public function create(): MatchEvent;
}
