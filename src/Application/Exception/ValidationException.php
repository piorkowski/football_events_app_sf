<?php

declare(strict_types=1);

namespace App\Application\Exception;

final class ValidationException extends \Exception
{
    public function __construct(
        public array $errors {
            get {
                return $this->errors;
            }
        },
    )
    {
        parent::__construct('Validation failed: '.implode(', ', $errors));
    }
}
