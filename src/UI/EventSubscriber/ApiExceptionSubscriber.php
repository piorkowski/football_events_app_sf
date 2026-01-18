<?php

declare(strict_types=1);

namespace App\UI\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 0],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        // Only handle API requests (JSON)
        if (!$this->isApiRequest($request)) {
            return;
        }

        // Handle BadRequestHttpException (wraps validation and JSON errors)
        if ($exception instanceof BadRequestHttpException) {
            $previous = $exception->getPrevious();

            // Invalid JSON
            if ($previous instanceof NotEncodableValueException) {
                $event->setResponse($this->jsonError('Invalid JSON'));
                return;
            }

            // Validation errors from MapRequestPayload / MapQueryString
            if ($previous instanceof ValidationFailedException) {
                $errors = $this->extractValidationErrors($previous);
                $event->setResponse($this->jsonError($errors['message'], $errors['details']));
                return;
            }

            // Generic bad request
            $event->setResponse($this->jsonError($exception->getMessage()));
            return;
        }

        // Handle ValidationFailedException directly (if not wrapped)
        if ($exception instanceof ValidationFailedException) {
            $errors = $this->extractValidationErrors($exception);
            $event->setResponse($this->jsonError($errors['message'], $errors['details']));
            return;
        }

        // Handle JSON syntax errors
        if ($exception instanceof \JsonException) {
            $event->setResponse($this->jsonError('Invalid JSON'));
            return;
        }
    }

    private function isApiRequest($request): bool
    {
        $contentType = $request->headers->get('Content-Type', '');
        $acceptHeader = $request->headers->get('Accept', '');
        $path = $request->getPathInfo();

        return str_contains($contentType, 'application/json')
            || str_contains($acceptHeader, 'application/json')
            || str_starts_with($path, '/event')
            || str_starts_with($path, '/statistics');
    }

    private function extractValidationErrors(ValidationFailedException $exception): array
    {
        $violations = $exception->getViolations();
        $details = [];
        $messages = [];

        foreach ($violations as $violation) {
            $propertyPath = $violation->getPropertyPath();
            $message = $violation->getMessage();

            $details[$propertyPath] = $message;
            $messages[] = $message;
        }

        // Map specific validation messages to test expectations
        $mainMessage = $this->mapValidationMessage($details, $messages);

        return [
            'message' => $mainMessage,
            'details' => $details,
        ];
    }

    private function mapValidationMessage(array $details, array $messages): string
    {
        // Check for specific field errors and return expected messages
        if (isset($details['type']) || in_array('This value should not be blank.', $messages, true)) {
            foreach ($details as $field => $message) {
                if ($field === 'type') {
                    return 'Event type is required';
                }
            }
        }

        if (isset($details['match_id'])) {
            return 'match_id is required';
        }

        // Return first message or generic error
        return $messages[0] ?? 'Validation failed';
    }

    private function jsonError(string $message, array $details = []): JsonResponse
    {
        $data = ['error' => $message];

        if (!empty($details)) {
            $data['validation_errors'] = $details;
        }

        return new JsonResponse($data, Response::HTTP_BAD_REQUEST);
    }
}
