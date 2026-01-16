<?php

declare(strict_types=1);

namespace App\Domain\Client;

enum ClientNotificationType: string
{
    case SMS = 'sms';
    case EMAIL = 'email';
}
