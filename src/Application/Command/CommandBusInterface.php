<?php

declare(strict_types=1);

namespace App\Application\Command;

use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Throwable;

interface CommandBusInterface
{
    /**
     * @throws Throwable
     * @throws ExceptionInterface
     */
    public function dispatch(CommandInterface $command): void;
}
