<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Tests\Unit;

use OmniCargo\NepalCan\Exceptions\WebhookException;
use OmniCargo\NepalCan\Resources\Webhook;
use OmniCargo\NepalCan\Services\WebhookService;
use OmniCargo\NepalCan\Tests\TestCase;

final class WebhookServiceTest extends TestCase
{
    private WebhookService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new WebhookService();
    }

    public function test_parse_single_order_webhook(): void
    {
        $fixture = $this->loadFixture('webhook_single_order.json');
        $payload = json_encode($fixture);

        $webhook = $this->service->parse($payload);

        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertEquals('123456', $webhook->orderId);
        $this->assertNull($webhook->orderIds);
        $this->assertEquals('Delivered', $webhook->status);
        $this->assertEquals('delivery_completed', $webhook->event);
        $this->assertNotEmpty($webhook->timestamp);
        $this->assertFalse($webhook->isTest);
    }

    public function test_parse_bulk_order_webhook(): void
    {
        $fixture = $this->loadFixture('webhook_bulk_order.json');
        $payload = json_encode($fixture);

        $webhook = $this->service->parse($payload);

        $this->assertNull($webhook->orderId);
        $this->assertCount(3, $webhook->orderIds);
        $this->assertEquals('Dispatched', $webhook->status);
        $this->assertEquals('order_dispatched', $webhook->event);
    }

    public function test_parse_test_webhook(): void
    {
        $fixture = $this->loadFixture('webhook_test_payload.json');
        $payload = json_encode($fixture);

        $webhook = $this->service->parse($payload);

        $this->assertTrue($webhook->isTest);
        $this->assertEquals('TEST-123456', $webhook->orderId);
    }

    public function test_parse_invalid_json_throws(): void
    {
        $this->expectException(WebhookException::class);
        $this->expectExceptionMessage('Invalid JSON payload');

        $this->service->parse('not valid json');
    }

    public function test_is_valid_user_agent(): void
    {
        $this->assertTrue($this->service->isValidUserAgent('NCM-Webhook/1.0'));
        $this->assertTrue($this->service->isValidUserAgent('NCM-Webhook/2.0'));
        $this->assertFalse($this->service->isValidUserAgent('Mozilla/5.0'));
        $this->assertFalse($this->service->isValidUserAgent(''));
    }

    public function test_is_bulk_detection(): void
    {
        $single = $this->service->parseFromArray(
            $this->loadFixture('webhook_single_order.json')
        );

        $bulk = $this->service->parseFromArray(
            $this->loadFixture('webhook_bulk_order.json')
        );

        $this->assertFalse($single->isBulk());
        $this->assertTrue($bulk->isBulk());
    }
}
