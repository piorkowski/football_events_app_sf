<?php

declare(strict_types=1);

namespace App\Application\Validator;

interface MatchEventValidatorInterface
{
    public function validate(string $type, array $data): void;
}
