<?php
declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Client\Client;

interface NotificatorInterface
{
    public function notify(Client $client): void;
}
