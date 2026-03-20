<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Services;

use OmniCargo\NepalCan\Exceptions\WebhookException;
use OmniCargo\NepalCan\Resources\Webhook;

final class WebhookService
{
    public function parse(string $payload): Webhook
    {
        $data = json_decode($payload, true);

        if ($data === null) {
            throw WebhookException::invalidPayload();
        }

        return Webhook::fromArray($data);
    }

    public function parseFromArray(array $data): Webhook
    {
        return Webhook::fromArray($data);
    }

    public function isValidUserAgent(string $userAgent): bool
    {
        return str_starts_with($userAgent, 'NCM-Webhook/');
    }
}
