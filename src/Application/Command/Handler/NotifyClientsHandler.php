<?php
declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\NotifyClientsCommand;
use App\Application\Service\NotificatorInterface;
use App\Domain\Client\Repository\ClientReadRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class NotifyClientsHandler
{
    public function __construct(
        private LoggerInterface $logger,
        private NotificatorInterface $notificator,
        private ClientReadRepositoryInterface $clientReadRepository,
    )
    {
    }

    public function __invoke(NotifyClientsCommand $command): void
    {
        try {
            $this->logger->info('Notifying clients');
            foreach ($this->clientReadRepository->activeClientsForNotification() as $client) {
                $this->logger->info('Notifying client {clientId}', ['clientId' => $client->getId()]);
                $this->notificator->notify($client);
            }
        } catch (\Throwable $e) {
            $this->logger->error('Error notifying clients', ['exception' => $e]);
        }
    }

}
