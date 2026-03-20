<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Services;

use OmniCargo\NepalCan\Http\HttpClient;
use OmniCargo\NepalCan\Resources\OrderStatus;
use OmniCargo\NepalCan\Support\Mapper;

final class TrackingService
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /** @return OrderStatus[] */
    public function getStatusHistory(int $orderId): array
    {
        $response = $this->http->get('/api/v1/order/status', ['id' => $orderId]);

        return Mapper::mapArray($response, fn (array $data) => OrderStatus::fromArray($data));
    }

    /** @return array<string, string> */
    public function getBulkStatuses(array $orderIds): array
    {
        $response = $this->http->post('/api/v1/orders/statuses', [
            'orders' => $orderIds,
        ]);

        return [
            'result' => $response['result'] ?? [],
            'errors' => $response['errors'] ?? [],
        ];
    }
}
