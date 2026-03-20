<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Tests\Unit;

use OmniCargo\NepalCan\Resources\Ticket;
use OmniCargo\NepalCan\Services\TicketService;
use OmniCargo\NepalCan\Tests\TestCase;

final class TicketServiceTest extends TestCase
{
    public function test_create_ticket_success(): void
    {
        $fixture = $this->loadFixture('ticket_create_success.json');
        $http = $this->mockHttpClient($fixture);

        $service = new TicketService($http);
        $ticket = $service->create(TicketService::TYPE_GENERAL, 'Please arrange delivery at the earliest');

        $this->assertInstanceOf(Ticket::class, $ticket);
        $this->assertEquals(123, $ticket->ticketId);
        $this->assertEquals('Ticket created', $ticket->message);
    }

    public function test_create_cod_transfer_ticket_success(): void
    {
        $fixture = $this->loadFixture('ticket_cod_create_success.json');
        $http = $this->mockHttpClient($fixture);

        $service = new TicketService($http);
        $ticket = $service->createCodTransfer('Nepal Bank Limited', 'John Doe', '1234567890');

        $this->assertInstanceOf(Ticket::class, $ticket);
        $this->assertEquals(124, $ticket->ticketId);
        $this->assertEquals('COD ticket created', $ticket->message);
    }

    public function test_close_ticket_success(): void
    {
        $fixture = $this->loadFixture('ticket_close_success.json');
        $http = $this->mockHttpClient($fixture);

        $service = new TicketService($http);
        $ticket = $service->close(123);

        $this->assertInstanceOf(Ticket::class, $ticket);
        $this->assertEquals(123, $ticket->ticketId);
        $this->assertEquals('Ticket closed', $ticket->message);
    }
}
