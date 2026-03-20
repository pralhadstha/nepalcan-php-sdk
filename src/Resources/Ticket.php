<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Resources;

final class Ticket
{
    public function __construct(
        public readonly int $ticketId,
        public readonly string $message,
        public readonly array $rawData
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            ticketId: (int) ($data['ticket'] ?? 0),
            message: $data['message'] ?? '',
            rawData: $data
        );
    }
}
