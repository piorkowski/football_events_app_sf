<?php

declare(strict_types=1);

namespace App\Domain\Client\VO;

use App\Domain\Client\ClientNotificationType;
use App\Domain\Shared\ValueObject;
use InvalidArgumentException;

final class ClientNotificationData extends ValueObject
{
    public function __construct(
        private readonly ClientNotificationType $type,
        private readonly string $value
    ) {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Notification data cannot be empty');
        }
    }

    public function type(): ClientNotificationType
    {
        return $this->type;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && $this->value === $other->value
            && $this->type->value === $other->type->value
        ;
    }

    public function __toString(): string
    {
        return $this->type->value . ' - ' . $this->value;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type->value,
            'value' => $this->value,
        ];
    }
}
