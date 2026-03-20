<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Webhooks;

use OmniCargo\NepalCan\Exceptions\WebhookException;
use OmniCargo\NepalCan\Resources\Webhook;

final class EventDispatcher
{
    /** @var array<string, WebhookHandlerInterface[]> */
    private array $handlers = [];

    public function subscribe(string $event, WebhookHandlerInterface $handler): self
    {
        $this->handlers[$event][] = $handler;

        return $this;
    }

    public function dispatch(Webhook $webhook): void
    {
        $handlers = $this->handlers[$webhook->event] ?? [];

        if ($handlers === []) {
            throw WebhookException::unhandledEvent($webhook->event);
        }

        foreach ($handlers as $handler) {
            $handler->handle($webhook);
        }
    }
}
