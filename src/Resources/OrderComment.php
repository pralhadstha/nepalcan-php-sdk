<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Resources;

final class OrderComment
{
    public function __construct(
        public readonly int $orderId,
        public readonly string $comments,
        public readonly string $addedBy,
        public readonly string $addedTime,
        public readonly array $rawData
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            orderId: (int) ($data['orderid'] ?? 0),
            comments: $data['comments'] ?? '',
            addedBy: $data['addedBy'] ?? '',
            addedTime: $data['added_time'] ?? '',
            rawData: $data
        );
    }
}
