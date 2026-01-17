<?php

declare(strict_types=1);

namespace App\Application\Query\Handler;

use App\Application\Query\GetMatchEventQuery;
use App\Domain\Event\MatchEvent;
use App\Domain\Event\Repository\MatchEventRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetMatchEventHandler
{
    public function __construct(
        private LoggerInterface $logger,
        private MatchEventRepositoryInterface $matchEventRepository,
    ) {
    }

    public function __invoke(GetMatchEventQuery $query): MatchEvent
    {
        $this->logger->info('Getting match event', ['matchEventId' => $query->matchEventId]);
        $event = $this->matchEventRepository->findByMatchEventId($query->matchEventId);

        return $event;
    }
}
