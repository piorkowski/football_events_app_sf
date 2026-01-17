<?php

declare(strict_types=1);

namespace App\Application\Exception;

final class ValidationException extends \Exception
{
    private array $errors {
        get {
            return $this->errors;
        }
    }

    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct('Validation failed: '.implode(', ', $errors));
    }
}
