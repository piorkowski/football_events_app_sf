<?php

declare(strict_types=1);

namespace App\Domain\Event\Validator;

interface MatchEventDataValidatorInterface
{
    /** @return array<string, string> */
    public function validate(array $data): array;
}
