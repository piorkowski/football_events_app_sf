<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Notification\Strategy;

use App\Domain\Client\Client;
use App\Domain\Client\ClientNotificationType;
use Psr\Log\LoggerInterface;

abstract class Notificator
{
    public function __construct(
        protected LoggerInterface $logger,
    ) {
    }

    abstract public function supports(ClientNotificationType $type): bool;

    abstract public function notify(Client $client): void;
}
