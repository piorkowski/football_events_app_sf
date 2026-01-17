<?php

declare(strict_types=1);

namespace App\Domain\Event\Validator;

use App\Application\Validator\MatchEventValidatorInterface;
use App\Domain\Event\Exception\MatchEventValidationException;
use App\UI\DTO\CommitEventDTO;

final class MatchEventValidator implements MatchEventValidatorInterface
{
    /** @var array<string, MatchEventDataValidatorInterface> */
    private array $validators;

    public function __construct(
        GoalDataValidator $goalValidator,
        FoulDataValidator $foulValidator,
    ) {
        $this->validators = [
            'goal' => $goalValidator,
            'foul' => $foulValidator,
        ];
    }

    public function validate(CommitEventDTO $dto): void
    {
        $errors = [];

        if (!isset($this->validators[$dto->type])) {
            throw new MatchEventValidationException(['type' => "Unknown event type: {$dto->type}"]);
        }

        $errors = $this->validators[$dto->type]->validate($dto->data);

        if (!empty($errors)) {
            throw new MatchEventValidationException($errors);
        }
    }
}
