<?php

declare(strict_types=1);

namespace App\Domain\Client;

use App\Domain\Client\VO\ClientId;

class Client
{
    public function __construct(
        private ClientId $id,
        private array    $notificaionData
    ) {}

    public function id(): ClientId
    {
        return $this->id;
    }

    public function addNotificationData(ClientNotificationType $key, mixed $value): void
    {
        $this->notificaionData[$key->value] = $value;
    }

    public function notificationData(): array
    {
        return $this->notificaionData;
    }
}
