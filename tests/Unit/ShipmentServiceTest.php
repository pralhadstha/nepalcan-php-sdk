<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Tests\Unit;

use OmniCargo\NepalCan\Exceptions\NotFoundException;
use OmniCargo\NepalCan\Exceptions\ValidationException;
use OmniCargo\NepalCan\Resources\Order;
use OmniCargo\NepalCan\Resources\OrderComment;
use OmniCargo\NepalCan\Services\ShipmentService;
use OmniCargo\NepalCan\Tests\TestCase;

final class ShipmentServiceTest extends TestCase
{
    public function test_create_shipment_success(): void
    {
        $fixture = $this->loadFixture('order_create_success.json');
        $http = $this->mockHttpClient($fixture);

        $service = new ShipmentService($http);
        $order = $service->create([
            'name' => 'John Doe',
            'phone' => '9847023226',
            'cod_charge' => '2200',
            'address' => 'Byas Pokhari',
            'fbranch' => 'TINKUNE',
            'branch' => 'BIRATNAGAR',
        ]);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals(747, $order->orderId);
    }

    public function test_create_shipment_validation_failure(): void
    {
        $this->expectException(ValidationException::class);

        $fixture = $this->loadFixture('error_400_validation.json');
        $http = $this->mockHttpClient($fixture, 400);

        $service = new ShipmentService($http);
        $service->create([]);
    }

    public function test_find_order_success(): void
    {
        $fixture = $this->loadFixture('order_detail_success.json');
        $http = $this->mockHttpClient($fixture);

        $service = new ShipmentService($http);
        $order = $service->find(134);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals(134, $order->orderId);
        $this->assertEquals('1710.00', $order->codCharge);
        $this->assertEquals('99.00', $order->deliveryCharge);
        $this->assertEquals('Delivered', $order->lastDeliveryStatus);
        $this->assertEquals('Completed', $order->paymentStatus);
    }

    public function test_find_order_not_found(): void
    {
        $this->expectException(NotFoundException::class);

        $fixture = $this->loadFixture('error_404.json');
        $http = $this->mockHttpClient($fixture, 404);

        $service = new ShipmentService($http);
        $service->find(99999);
    }

    public function test_get_comments_success(): void
    {
        $fixture = $this->loadFixture('order_comments_success.json');
        $http = $this->mockHttpClient($fixture);

        $service = new ShipmentService($http);
        $comments = $service->getComments(134);

        $this->assertCount(2, $comments);
        $this->assertInstanceOf(OrderComment::class, $comments[0]);
        $this->assertEquals(134, $comments[0]->orderId);
        $this->assertNotEmpty($comments[0]->comments);
        $this->assertEquals('NCM Staff', $comments[0]->addedBy);
    }

    public function test_get_bulk_comments_success(): void
    {
        $fixture = $this->loadFixture('order_bulk_comments_success.json');
        $http = $this->mockHttpClient($fixture);

        $service = new ShipmentService($http);
        $comments = $service->getBulkComments();

        $this->assertCount(2, $comments);
        $this->assertInstanceOf(OrderComment::class, $comments[0]);
        $this->assertEquals(123, $comments[0]->orderId);
    }

    public function test_add_comment_success(): void
    {
        $fixture = $this->loadFixture('comment_add_success.json');
        $http = $this->mockHttpClient($fixture);

        $service = new ShipmentService($http);
        $result = $service->addComment(1234567, 'Test comment from api');

        $this->assertEquals('Comment successfully created', $result['message']);
    }

    public function test_return_order_success(): void
    {
        $fixture = $this->loadFixture('return_order_success.json');
        $http = $this->mockHttpClient($fixture);

        $service = new ShipmentService($http);
        $result = $service->returnOrder(4041, 'Customer refused the delivery');

        $this->assertEquals('Order marked for return successfully', $result['message']);
        $this->assertEquals(4041, $result['order']);
        $this->assertTrue($result['vendor_return']);
    }

    public function test_create_exchange_success(): void
    {
        $fixture = $this->loadFixture('exchange_create_success.json');
        $http = $this->mockHttpClient($fixture);

        $service = new ShipmentService($http);
        $result = $service->createExchange(4041);

        $this->assertEquals('Exchange orders created', $result['message']);
        $this->assertEquals(4567, $result['cust_order']);
        $this->assertEquals(4568, $result['ven_order']);
    }

    public function test_redirect_order_success(): void
    {
        $fixture = $this->loadFixture('redirect_order_success.json');
        $http = $this->mockHttpClient($fixture);

        $service = new ShipmentService($http);
        $result = $service->redirect(4041, [
            'name' => 'Jane Doe',
            'phone' => '9800000000',
            'address' => 'New Address',
        ]);

        $this->assertEquals('Order redirected successfully', $result['message']);
    }
}
