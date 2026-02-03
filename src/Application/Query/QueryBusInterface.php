<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Infrastructure\Exception\QueryBusException;

interface QueryBusInterface
{
    /**
     * @throws QueryBusException
     */
    public function ask(QueryInterface $query): mixed;
}
