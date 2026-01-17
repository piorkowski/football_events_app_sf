<?php

declare(strict_types=1);

namespace App\Domain\MatchEvent\Validator;

interface MatchEventDataValidatorInterface
{
    public function supports(string $eventType): bool;

    /** @return array<string, string> */
    public function validate(array $data): array;
}
