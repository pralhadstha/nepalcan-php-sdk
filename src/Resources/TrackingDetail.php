<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Resources;

final class TrackingDetail
{
    public function __construct(
        public readonly string $trackId,
        public readonly string $lastDeliveryStatus,
        public readonly string $vendor,
        public readonly string $vendorPhone,
        public readonly string $receiver,
        public readonly string $receiverPhone,
        public readonly int|string $receiverAddress,
        public readonly string $destination,
        public readonly string $weight,
        public readonly string $deliveryCharge,
        public readonly string $codCharge,
        public readonly array $statusHistory,
        public readonly array $rawData,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $details = $data['details'] ?? [];
        $statusHistory = $data['status'] ?? [];

        return new self(
            trackId: $details['trackid'] ?? '',
            lastDeliveryStatus: $details['last_delivery_status'] ?? '',
            vendor: $details['vendor'] ?? '',
            vendorPhone: $details['vendor_phone'] ?? '',
            receiver: $details['receiver'] ?? '',
            receiverPhone: $details['receiver_phone'] ?? '',
            receiverAddress: $details['receiver_address'] ?? '',
            destination: $details['destination'] ?? '',
            weight: (string) ($details['weight'] ?? ''),
            deliveryCharge: (string) ($details['delivery_charge'] ?? ''),
            codCharge: (string) ($details['cod_charge'] ?? ''),
            statusHistory: $statusHistory,
            rawData: $data,
        );
    }
}
