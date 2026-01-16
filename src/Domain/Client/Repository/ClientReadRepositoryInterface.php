<?php

namespace App\Domain\Client\Repository;

interface ClientReadRepositoryInterface
{
    public function activeClientsForNotification(): array;
}
