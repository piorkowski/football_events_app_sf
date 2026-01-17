<?php

declare(strict_types=1);

namespace App\Domain\Match\VO;

use App\Domain\Shared\ValueObject;

final class MatchId extends ValueObject
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty(trim($value))) {
            throw new \InvalidArgumentException('Match ID cannot be empty');
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
