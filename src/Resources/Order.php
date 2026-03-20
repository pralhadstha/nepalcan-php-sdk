<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Resources;

final class Order
{
    public function __construct(
        public readonly int $orderId,
        public readonly string $codCharge,
        public readonly string $deliveryCharge,
        public readonly string $lastDeliveryStatus,
        public readonly string $paymentStatus,
        public readonly array $rawData
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            orderId: (int) ($data['orderid'] ?? $data['orderId'] ?? 0),
            codCharge: (string) ($data['cod_charge'] ?? ''),
            deliveryCharge: (string) ($data['delivery_charge'] ?? ''),
            lastDeliveryStatus: $data['last_delivery_status'] ?? '',
            paymentStatus: $data['payment_status'] ?? '',
            rawData: $data
        );
    }
}
