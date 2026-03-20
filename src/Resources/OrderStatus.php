<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Resources;

final class OrderStatus
{
    public function __construct(
        public readonly int $orderId,
        public readonly string $status,
        public readonly string $addedTime,
        public readonly array $rawData
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            orderId: (int) ($data['orderid'] ?? 0),
            status: $data['status'] ?? '',
            addedTime: $data['added_time'] ?? '',
            rawData: $data
        );
    }
}
