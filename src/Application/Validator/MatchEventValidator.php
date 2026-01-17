<?php

declare(strict_types=1);

namespace App\Application\Validator;

use App\Application\Exception\ValidationException;
use App\Domain\MatchEvent\Validator\MatchEventDataValidatorInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class MatchEventValidator implements MatchEventValidatorInterface
{
    /** @param iterable<MatchEventDataValidatorInterface> $validators */
    public function __construct(
        #[AutowireIterator('match_event.validator')]
        private iterable $validators,
    ) {
    }

    public function validate(string $type, array $data): void
    {
        foreach ($this->validators as $validator) {
            if ($validator->supports($type)) {
                $errors = $validator->validate($data);

                if (!empty($errors)) {
                    throw new ValidationException($errors);
                }

                return;
            }
        }

        throw new ValidationException(['type' => "Unknown event type: {$type}"]);
    }
}
