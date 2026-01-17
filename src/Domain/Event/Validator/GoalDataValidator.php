<?php

declare(strict_types=1);

namespace App\Domain\Event\Validator;

final class GoalDataValidator implements MatchEventDataValidatorInterface
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['match_id'])) {
            $errors['match_id'] = 'Match ID is required';
        }

        if (empty($data['team_id'])) {
            $errors['team_id'] = 'Team ID is required';
        }

        if (empty($data['scorer_id'])) {
            $errors['scorer_id'] = 'Scorer ID is required';
        }

        if (!isset($data['minute'])) {
            $errors['minute'] = 'Minute is required';
        } elseif ($data['minute'] < 0 || $data['minute'] > 120) {
            $errors['minute'] = 'Minute must be between 0 and 120';
        }

        if (!isset($data['second'])) {
            $errors['second'] = 'Second is required';
        } elseif ($data['second'] < 0 || $data['second'] > 59) {
            $errors['second'] = 'Second must be between 0 and 59';
        }

        return $errors;
    }
}
