<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Resources;

final class Branch
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $phone,
        public readonly string $district,
        public readonly string $region,
        public readonly array $coveredAreas,
        public readonly array $rawData
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: $data['name'] ?? '',
            phone: $data['phone'] ?? '',
            district: $data['district'] ?? '',
            region: $data['region'] ?? '',
            coveredAreas: $data['covered_areas'] ?? [],
            rawData: $data
        );
    }
}
