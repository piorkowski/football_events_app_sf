<?php

declare(strict_types=1);

namespace App\Application\Validator;

use App\Application\Exception\ValidationException;
use App\UI\DTO\CommitEventDTO;

interface MatchEventValidatorInterface
{
    /** @throws ValidationException */
    public function validate(CommitEventDTO $dto): void;
}
