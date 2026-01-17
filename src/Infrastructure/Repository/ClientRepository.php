<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Client\Client;
use App\Domain\Client\ClientNotificationType;
use App\Domain\Client\Repository\ClientReadRepositoryInterface;
use App\Domain\Client\VO\ClientId;
use App\Domain\Client\VO\ClientNotificationData;

class ClientRepository implements ClientReadRepositoryInterface
{
    public function activeClientsForNotification(): array
    {
        return [
            new Client(new ClientId('client1'), [
                new ClientNotificationData(ClientNotificationType::EMAIL, 'test@test.com'),
                new ClientNotificationData(ClientNotificationType::SMS, '123456789'),
            ]),
            new Client(new ClientId('client2'), [
                new ClientNotificationData(ClientNotificationType::EMAIL, 'test2@test.com'),
                new ClientNotificationData(ClientNotificationType::SMS, '987654321'),
            ]),
        ];
    }
}
