<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Tests\Unit;

use OmniCargo\NepalCan\Client;
use OmniCargo\NepalCan\Config;
use OmniCargo\NepalCan\Services\BranchService;
use OmniCargo\NepalCan\Services\RateService;
use OmniCargo\NepalCan\Services\ShipmentService;
use OmniCargo\NepalCan\Services\StaffService;
use OmniCargo\NepalCan\Services\TicketService;
use OmniCargo\NepalCan\Services\TrackingService;
use OmniCargo\NepalCan\Services\WebhookService;
use OmniCargo\NepalCan\Tests\TestCase;

final class ClientTest extends TestCase
{
    public function test_client_initialization(): void
    {
        $client = new Client('test-token');

        $this->assertInstanceOf(Client::class, $client);
    }

    public function test_client_exposes_all_services(): void
    {
        $client = new Client('test-token');

        $this->assertInstanceOf(ShipmentService::class, $client->shipments);
        $this->assertInstanceOf(TrackingService::class, $client->tracking);
        $this->assertInstanceOf(RateService::class, $client->rates);
        $this->assertInstanceOf(BranchService::class, $client->branches);
        $this->assertInstanceOf(TicketService::class, $client->tickets);
        $this->assertInstanceOf(StaffService::class, $client->staff);
        $this->assertInstanceOf(WebhookService::class, $client->webhooks);
    }

    public function test_client_with_custom_base_url(): void
    {
        $client = new Client('test-token', Config::BASE_URL_PRODUCTION);

        $this->assertInstanceOf(Client::class, $client);
    }
}
