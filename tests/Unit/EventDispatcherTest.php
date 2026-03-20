<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Tests\Unit;

use OmniCargo\NepalCan\Exceptions\WebhookException;
use OmniCargo\NepalCan\Resources\Webhook;
use OmniCargo\NepalCan\Tests\TestCase;
use OmniCargo\NepalCan\Webhooks\EventDispatcher;
use OmniCargo\NepalCan\Webhooks\WebhookEvent;
use OmniCargo\NepalCan\Webhooks\WebhookHandlerInterface;

final class EventDispatcherTest extends TestCase
{
    private EventDispatcher $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dispatcher = new EventDispatcher();
    }

    public function test_dispatch_routes_to_registered_handler(): void
    {
        $handled = false;

        $handler = new class ($handled) implements WebhookHandlerInterface {
            private bool $handled;

            public function __construct(bool &$handled)
            {
                $this->handled = &$handled;
            }

            public function handle(Webhook $webhook): void
            {
                $this->handled = true;
            }
        };

        $this->dispatcher->subscribe(WebhookEvent::DELIVERY_COMPLETED, $handler);

        $webhook = Webhook::fromArray($this->loadFixture('webhook_single_order.json'));
        $this->dispatcher->dispatch($webhook);

        $this->assertTrue($handled);
    }

    public function test_dispatch_calls_multiple_handlers_for_same_event(): void
    {
        $callCount = 0;

        $handler = new class ($callCount) implements WebhookHandlerInterface {
            private int $callCount;

            public function __construct(int &$callCount)
            {
                $this->callCount = &$callCount;
            }

            public function handle(Webhook $webhook): void
            {
                $this->callCount++;
            }
        };

        $this->dispatcher->subscribe(WebhookEvent::DELIVERY_COMPLETED, $handler);
        $this->dispatcher->subscribe(WebhookEvent::DELIVERY_COMPLETED, $handler);

        $webhook = Webhook::fromArray($this->loadFixture('webhook_single_order.json'));
        $this->dispatcher->dispatch($webhook);

        $this->assertEquals(2, $callCount);
    }

    public function test_dispatch_throws_when_no_handlers_registered(): void
    {
        $this->expectException(WebhookException::class);
        $this->expectExceptionMessage('No handlers registered for webhook event: delivery_completed');

        $webhook = Webhook::fromArray($this->loadFixture('webhook_single_order.json'));
        $this->dispatcher->dispatch($webhook);
    }

    public function test_handler_receives_correct_webhook(): void
    {
        $receivedWebhook = null;

        $handler = new class ($receivedWebhook) implements WebhookHandlerInterface {
            private ?Webhook $receivedWebhook;

            public function __construct(?Webhook &$receivedWebhook)
            {
                $this->receivedWebhook = &$receivedWebhook;
            }

            public function handle(Webhook $webhook): void
            {
                $this->receivedWebhook = $webhook;
            }
        };

        $this->dispatcher->subscribe(WebhookEvent::DELIVERY_COMPLETED, $handler);

        $webhook = Webhook::fromArray($this->loadFixture('webhook_single_order.json'));
        $this->dispatcher->dispatch($webhook);

        $this->assertNotNull($receivedWebhook);
        $this->assertEquals('123456', $receivedWebhook->orderId);
        $this->assertEquals('Delivered', $receivedWebhook->status);
        $this->assertEquals('delivery_completed', $receivedWebhook->event);
    }

    public function test_subscribe_returns_self_for_fluent_interface(): void
    {
        $handler = new class () implements WebhookHandlerInterface {
            public function handle(Webhook $webhook): void
            {
            }
        };

        $result = $this->dispatcher->subscribe(WebhookEvent::DELIVERY_COMPLETED, $handler);

        $this->assertSame($this->dispatcher, $result);
    }

    public function test_handlers_for_different_events_are_independent(): void
    {
        $deliveryHandled = false;
        $dispatchHandled = false;

        $deliveryHandler = new class ($deliveryHandled) implements WebhookHandlerInterface {
            private bool $handled;

            public function __construct(bool &$handled)
            {
                $this->handled = &$handled;
            }

            public function handle(Webhook $webhook): void
            {
                $this->handled = true;
            }
        };

        $dispatchHandler = new class ($dispatchHandled) implements WebhookHandlerInterface {
            private bool $handled;

            public function __construct(bool &$handled)
            {
                $this->handled = &$handled;
            }

            public function handle(Webhook $webhook): void
            {
                $this->handled = true;
            }
        };

        $this->dispatcher->subscribe(WebhookEvent::DELIVERY_COMPLETED, $deliveryHandler);
        $this->dispatcher->subscribe(WebhookEvent::ORDER_DISPATCHED, $dispatchHandler);

        $webhook = Webhook::fromArray($this->loadFixture('webhook_single_order.json'));
        $this->dispatcher->dispatch($webhook);

        $this->assertTrue($deliveryHandled);
        $this->assertFalse($dispatchHandled);
    }
}
