<?php

declare(strict_types=1);

namespace App\UI\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CommitEventDTO
{
    public function __construct(
        #[Assert\NotBlank()]
        public string $type,
        public \DateTimeImmutable $date = new \DateTimeImmutable(),
        public array $data = [],
    ) {
    }
}
