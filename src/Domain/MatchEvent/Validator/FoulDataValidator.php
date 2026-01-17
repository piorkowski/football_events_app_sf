<?php

declare(strict_types=1);

namespace App\Domain\MatchEvent\Validator;

use App\Domain\MatchEvent\EventType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('match_event.validator')]
final class FoulDataValidator implements MatchEventDataValidatorInterface
{
    public function supports(string $eventType): bool
    {
        return $eventType === EventType::FOUL->value;
    }

    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['match_id'])) {
            $errors['match_id'] = 'Match ID is required';
        }

        if (empty($data['team_id'])) {
            $errors['team_id'] = 'Team ID is required';
        }

        if (empty($data['committed_by'])) {
            $errors['committed_by'] = 'Committed by player ID is required';
        }

        if (!isset($data['minute'])) {
            $errors['minute'] = 'Minute is required';
        } elseif ($data['minute'] < 0) {
            $errors['minute'] = 'Minute must be bigger than 0';
        }

        if (!isset($data['second'])) {
            $errors['second'] = 'Second is required';
        } elseif ($data['second'] < 0 || $data['second'] > 59) {
            $errors['second'] = 'Second must be between 0 and 59';
        }

        return $errors;
    }
}
