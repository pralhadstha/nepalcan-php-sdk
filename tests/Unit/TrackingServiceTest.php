<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Tests\Unit;

use OmniCargo\NepalCan\Resources\OrderStatus;
use OmniCargo\NepalCan\Services\TrackingService;
use OmniCargo\NepalCan\Tests\TestCase;

final class TrackingServiceTest extends TestCase
{
    public function test_get_status_history_success(): void
    {
        $fixture = $this->loadFixture('order_status_history_success.json');
        $http = $this->mockHttpClient($fixture);

        $service = new TrackingService($http);
        $statuses = $service->getStatusHistory(134);

        $this->assertCount(4, $statuses);
        $this->assertInstanceOf(OrderStatus::class, $statuses[0]);
        $this->assertEquals(134, $statuses[0]->orderId);
        $this->assertEquals('Delivered', $statuses[0]->status);
        $this->assertNotEmpty($statuses[0]->addedTime);
        $this->assertEquals('Pickup Order Created', $statuses[3]->status);
    }

    public function test_get_bulk_statuses_success(): void
    {
        $fixture = $this->loadFixture('bulk_statuses_success.json');
        $http = $this->mockHttpClient($fixture);

        $service = new TrackingService($http);
        $result = $service->getBulkStatuses([4041, 3855, 4032, 3841, 3842, 4042]);

        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('errors', $result);
        $this->assertEquals('Pickup Order Created', $result['result']['4041']);
        $this->assertEquals('Delivered', $result['result']['3841']);
        $this->assertContains(4042, $result['errors']);
    }
}
