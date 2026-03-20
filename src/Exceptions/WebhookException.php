<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Exceptions;

final class WebhookException extends ApiException
{
    public static function invalidPayload(string $message = 'Invalid JSON payload'): self
    {
        return new self($message, 0, []);
    }

    public static function unhandledEvent(string $event): self
    {
        return new self("No handlers registered for webhook event: {$event}", 0, []);
    }
}
