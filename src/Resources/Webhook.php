<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Resources;

final class Webhook
{
    public function __construct(
        public readonly ?string $orderId,
        public readonly ?array $orderIds,
        public readonly string $status,
        public readonly string $event,
        public readonly string $timestamp,
        public readonly bool $isTest,
        public readonly array $rawData
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            orderId: $data['order_id'] ?? null,
            orderIds: $data['order_ids'] ?? null,
            status: $data['status'] ?? '',
            event: $data['event'] ?? '',
            timestamp: $data['timestamp'] ?? '',
            isTest: (bool) ($data['test'] ?? false),
            rawData: $data
        );
    }

    public function isBulk(): bool
    {
        return $this->orderIds !== null;
    }
}
