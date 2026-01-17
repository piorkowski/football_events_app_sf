<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Notification\Strategy;

use App\Domain\Client\Client;
use App\Infrastructure\Exception\UnableToMatchStrategyException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class SenderNotificationStrategyLocator
{
    /**
     * @param SenderNotificationStrategyInterface[] $notificationStrategies
     */
    public function __construct(
        #[AutowireIterator('notification.strategy')]
        private iterable $notificationStrategies,
    ) {
    }

    /**
     * @throws UnableToMatchStrategyException
     */
    public function locate(Client $client): SenderNotificationStrategyInterface
    {
        foreach ($this->notificationStrategies as $strategy) {
            if (array_any($client->notificationData(), fn ($notificationData) => $strategy->supports($notificationData))) {
                return $strategy;
            }
        }

        throw new UnableToMatchStrategyException();
    }
}
