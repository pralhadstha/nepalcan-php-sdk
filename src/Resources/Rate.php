<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Resources;

final class Rate
{
    public function __construct(
        public readonly string $origin,
        public readonly string $destination,
        public readonly string $type,
        public readonly string $charge,
        public readonly array $rawData
    ) {}

    public static function fromArray(array $data, string $origin = '', string $destination = '', string $type = ''): self
    {
        return new self(
            origin: $origin,
            destination: $destination,
            type: $type,
            charge: (string) ($data['charge'] ?? $data['delivery_charge'] ?? ''),
            rawData: $data
        );
    }
}
