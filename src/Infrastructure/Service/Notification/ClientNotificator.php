<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Notification;

use App\Application\Service\NotificatorInterface;
use App\Domain\Client\Client;
use App\Infrastructure\Service\Notification\Strategy\SenderNotificationStrategyLocator;
use Psr\Log\LoggerInterface;

class ClientNotificator implements NotificatorInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected SenderNotificationStrategyLocator $locator,
    ) {
    }

    public function notify(Client $client): void
    {
        $this->logger->info('Notifying client {clientId}', ['clientId' => $client->id()->value()]);

    }
}
