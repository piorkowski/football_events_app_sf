<?php

declare(strict_types=1);

namespace App\Domain\Event\VO;

use App\Domain\Shared\ValueObject;
use InvalidArgumentException;

final class MatchEventId extends ValueObject
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Match Event ID cannot be empty');
        }

        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
