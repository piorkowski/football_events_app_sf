<?php
declare(strict_types=1);

namespace App\UI\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GetStatisticDTO
{
    public function __construct(
        #[Assert\NotBlank()]
        public string $matchId,
        public ?string $teamId
    ) {}
}
