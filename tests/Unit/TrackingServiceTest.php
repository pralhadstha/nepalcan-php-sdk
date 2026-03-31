<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Tests\Unit;

use OmniCargo\NepalCan\Resources\OrderStatus;
use OmniCargo\NepalCan\Resources\TrackingDetail;
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

    public function test_track_by_tracking_id_success(): void
    {
        $fixture = $this->loadFixture('track_by_tracking_id_success.json');
        $http = $this->mockHttpClient($fixture);

        $service = new TrackingService($http);
        $tracking = $service->track('8D634706B3394C3');

        $this->assertInstanceOf(TrackingDetail::class, $tracking);
        $this->assertEquals('8D634706B3394C3', $tracking->trackId);
        $this->assertEquals('Pickup Order Created', $tracking->lastDeliveryStatus);
        $this->assertEquals('Demo Vendor', $tracking->vendor);
        $this->assertEquals('Saroj Tamang', $tracking->receiver);
        $this->assertEquals('9842227083', $tracking->receiverPhone);
        $this->assertEquals('TINKUNE', $tracking->destination);
        $this->assertEquals('1.00', $tracking->weight);
        $this->assertEquals('99.00', $tracking->deliveryCharge);
        $this->assertEquals('3208.00', $tracking->codCharge);
        $this->assertCount(1, $tracking->statusHistory);
        $this->assertEquals('Pickup Order Created - 2026-03-23 04:43 AM', $tracking->statusHistory[0]);
    }
}
