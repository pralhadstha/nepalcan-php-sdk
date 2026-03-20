<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan;

use GuzzleHttp\Client as GuzzleClient;
use OmniCargo\NepalCan\Http\HttpClient;
use OmniCargo\NepalCan\Services\BranchService;
use OmniCargo\NepalCan\Services\RateService;
use OmniCargo\NepalCan\Services\ShipmentService;
use OmniCargo\NepalCan\Services\StaffService;
use OmniCargo\NepalCan\Services\TicketService;
use OmniCargo\NepalCan\Services\TrackingService;
use OmniCargo\NepalCan\Services\WebhookService;

final class Client
{
    private HttpClient $http;

    public readonly ShipmentService $shipments;
    public readonly TrackingService $tracking;
    public readonly RateService $rates;
    public readonly BranchService $branches;
    public readonly TicketService $tickets;
    public readonly StaffService $staff;
    public readonly WebhookService $webhooks;

    public function __construct(string $apiToken, string $baseUrl = Config::BASE_URL_SANDBOX, ?GuzzleClient $guzzle = null)
    {
        $config = new Config($apiToken, $baseUrl);
        $this->http = new HttpClient($config, $guzzle);

        $this->shipments = new ShipmentService($this->http);
        $this->tracking = new TrackingService($this->http);
        $this->rates = new RateService($this->http);
        $this->branches = new BranchService($this->http);
        $this->tickets = new TicketService($this->http);
        $this->staff = new StaffService($this->http);
        $this->webhooks = new WebhookService();
    }
}
