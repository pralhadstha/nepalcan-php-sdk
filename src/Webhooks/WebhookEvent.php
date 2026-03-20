<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Webhooks;

final class WebhookEvent
{
    public const PICKUP_COMPLETED = 'pickup_completed';
    public const SENT_FOR_DELIVERY = 'sent_for_delivery';
    public const ORDER_DISPATCHED = 'order_dispatched';
    public const ORDER_ARRIVED = 'order_arrived';
    public const DELIVERY_COMPLETED = 'delivery_completed';
}
