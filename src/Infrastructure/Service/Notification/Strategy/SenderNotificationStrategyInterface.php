<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Notification\Strategy;

use App\Domain\Client\Client;
use App\Domain\Client\ClientNotificationType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('notification.strategy')]
interface SenderNotificationStrategyInterface
{
    public function supports(ClientNotificationType $type): bool;

    public function notify(Client $client): void;
}
