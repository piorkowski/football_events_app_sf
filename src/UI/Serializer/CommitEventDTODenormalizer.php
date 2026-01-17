<?php

declare(strict_types=1);

namespace App\UI\Serializer;

use App\UI\DTO\CommitEventDTO;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

#[AutoconfigureTag('serializer.normalizer')]
final class CommitEventDTODenormalizer implements DenormalizerInterface
{
    private const array KNOWN_FIELDS = ['type', 'date'];

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): CommitEventDTO
    {
        $eventData = array_diff_key($data, array_flip(self::KNOWN_FIELDS));

        return new CommitEventDTO(
            type: $data['type'] ?? '',
            date: isset($data['date']) ? new \DateTimeImmutable($data['date']) : new \DateTimeImmutable(),
            data: $eventData,
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return CommitEventDTO::class === $type;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [CommitEventDTO::class => true];
    }
}
