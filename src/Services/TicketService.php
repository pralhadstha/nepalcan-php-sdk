<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Services;

use OmniCargo\NepalCan\Http\HttpClient;
use OmniCargo\NepalCan\Resources\Ticket;
use OmniCargo\NepalCan\Support\Mapper;

final class TicketService
{
    public const TYPE_GENERAL = 'General';
    public const TYPE_ORDER_PROCESSING = 'Order Processing';
    public const TYPE_RETURN = 'Return';
    public const TYPE_PICKUP = 'Pickup';

    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    public function create(string $ticketType, string $message): Ticket
    {
        $response = $this->http->post('/api/v2/vendor/ticket/create', [
            'ticket_type' => $ticketType,
            'message' => $message,
        ]);

        return Mapper::mapSingle($response, fn(array $data) => Ticket::fromArray($data));
    }

    public function createCodTransfer(string $bankName, string $accountName, string $accountNumber): Ticket
    {
        $response = $this->http->post('/api/v2/vendor/ticket/cod/create', [
            'bankName' => $bankName,
            'bankAccountName' => $accountName,
            'bankAccountNumber' => $accountNumber,
        ]);

        return Mapper::mapSingle($response, fn(array $data) => Ticket::fromArray($data));
    }

    public function close(int $ticketId): Ticket
    {
        $response = $this->http->post("/api/v2/vendor/ticket/close/{$ticketId}");

        return Mapper::mapSingle($response, fn(array $data) => Ticket::fromArray($data));
    }
}
