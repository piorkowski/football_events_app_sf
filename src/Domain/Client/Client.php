<?php

declare(strict_types=1);

namespace App\Domain\Client;

use App\Domain\Client\VO\ClientId;

class Client
{
    public function __construct(
        private readonly ClientId $id,
        private array $notificationData = [],
    ) {
    }

    public function id(): ClientId
    {
        return $this->id;
    }

    public function addNotificationData(ClientNotificationType $key, string $value): void
    {
        $this->notificationData[$key->value] = $value;
    }

    public function notificationData(): array
    {
        return $this->notificationData;
    }
}
