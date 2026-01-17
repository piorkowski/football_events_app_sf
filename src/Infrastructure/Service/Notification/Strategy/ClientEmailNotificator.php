<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Notification\Strategy;

use App\Domain\Client\Client;
use App\Domain\Client\ClientNotificationType;

final class ClientEmailNotificator extends Notificator implements SenderNotificationStrategyInterface
{
    public function supports(ClientNotificationType $type): bool
    {
        return ClientNotificationType::EMAIL === $type;
    }

    public function notify(Client $client): void
    {
        $this->logger->info('Client %s has been notified', [$client->id()->value()]);
        echo printf('Client %s has been notified', $client->id()->value());
    }
}
