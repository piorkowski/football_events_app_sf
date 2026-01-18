<?php

declare(strict_types=1);

namespace App\UI\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GetStatisticDTO
{
    public function __construct(
        #[Assert\NotBlank(message: 'Match id is required')]
        public ?string $match_id,
        public ?string $team_id,
    ) {
    }
}
