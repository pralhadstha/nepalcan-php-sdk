<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Webhooks;

use OmniCargo\NepalCan\Resources\Webhook;

interface WebhookHandlerInterface
{
    public function handle(Webhook $webhook): void;
}
