<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Services;

use OmniCargo\NepalCan\Http\HttpClient;
use OmniCargo\NepalCan\Resources\Order;
use OmniCargo\NepalCan\Resources\OrderComment;
use OmniCargo\NepalCan\Support\Mapper;

final class ShipmentService
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    public function create(array $params): Order
    {
        $response = $this->http->post('/api/v1/order/create', $params);

        return Order::fromArray([
            'orderid' => $response['orderid'] ?? 0,
            'message' => $response['Message'] ?? '',
        ] + $response);
    }

    public function find(int $orderId): Order
    {
        $response = $this->http->get('/api/v1/order', ['id' => $orderId]);

        return Mapper::mapSingle($response, fn(array $data) => Order::fromArray($data));
    }

    /** @return OrderComment[] */
    public function getComments(int $orderId): array
    {
        $response = $this->http->get('/api/v1/order/comment', ['id' => $orderId]);

        return Mapper::mapArray($response, fn(array $data) => OrderComment::fromArray($data));
    }

    /** @return OrderComment[] */
    public function getBulkComments(): array
    {
        $response = $this->http->get('/api/v1/order/getbulkcomments');

        return Mapper::mapArray($response, fn(array $data) => OrderComment::fromArray($data));
    }

    public function addComment(int $orderId, string $comment): array
    {
        return $this->http->post('/api/v1/comment', [
            'orderid' => (string) $orderId,
            'comments' => $comment,
        ]);
    }

    public function returnOrder(int $orderId, ?string $comment = null): array
    {
        $params = ['pk' => $orderId];

        if ($comment !== null) {
            $params['comment'] = $comment;
        }

        return $this->http->post('/api/v2/vendor/order/return', $params);
    }

    public function createExchange(int $orderId): array
    {
        return $this->http->post('/api/v2/vendor/order/exchange-create', [
            'pk' => $orderId,
        ]);
    }

    public function redirect(int $orderId, array $params): array
    {
        return $this->http->post('/api/v2/vendor/order/redirect', array_merge(
            ['pk' => $orderId],
            $params
        ));
    }
}
