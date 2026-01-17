<?php

declare(strict_types=1);

namespace App\UI\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

final class CreateMatchEventResponse extends JsonResponse
{
    public function __construct(mixed $data = null, int $status = 200, array $headers = [], bool $json = false)
    {
        parent::__construct($data, $status, $headers, $json);
    }

    public function __invoke(): string
    {
        try {
            return json_encode([
                'status' => 'success',
                'message' => 'Event saved successfully',
                'event' => $this->event?->toArray(),
            ], JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JSON_THROW_ON_ERROR);
        }
    }
}
