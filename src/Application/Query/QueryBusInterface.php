<?php

declare(strict_types=1);

namespace App\Application\Query;

interface QueryBusInterface
{
    public function ask(QueryInterface $query): mixed;
}
