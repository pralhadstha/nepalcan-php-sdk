# Nepal Can Move (NCM) PHP SDK

[![PHP Version](https://img.shields.io/packagist/php-v/pralhadstha/nepal-can-php-sdk)](https://packagist.org/packages/pralhadstha/nepal-can-php-sdk)
[![Latest Version](https://img.shields.io/packagist/v/pralhadstha/nepal-can-php-sdk)](https://packagist.org/packages/pralhadstha/nepal-can-php-sdk)
[![License](https://img.shields.io/packagist/l/pralhadstha/nepal-can-php-sdk)](https://packagist.org/packages/pralhadstha/nepal-can-php-sdk)

A PHP SDK for integrating with the [Nepal Can Move (NCM)](https://nepalcanmove.com) shipping and courier API. Manage shipments, track orders, calculate delivery rates, handle COD (Cash on Delivery) payments, and receive real-time webhook notifications for your e-commerce platform in Nepal.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Branches](#branches)
  - [Shipping Rates](#shipping-rates)
  - [Create Order](#create-order)
  - [Get Order Details](#get-order-details)
  - [Order Comments](#order-comments)
  - [Tracking](#tracking)
  - [Returns & Exchanges](#returns--exchanges)
  - [Tickets](#tickets)
  - [Staff](#staff)
- [Webhooks](#webhooks)
  - [Event Dispatcher](#event-dispatcher)
  - [Laravel Example](#laravel-example)
  - [Idempotency](#idempotency)
- [Error Handling](#error-handling)
- [API Limits](#api-limits)
- [Testing](#testing)
- [Code Style](#code-style)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

## Features

- **Shipment Management** - Create, find, return, exchange, and redirect orders
- **Order Tracking** - Status history and bulk status checks
- **Rate Calculation** - Delivery charges for 4 delivery types (Door2Door, Branch2Door, Door2Branch, Branch2Branch)
- **Branch Listing** - Get all NCM branches with contact details and covered areas
- **COD Support** - Cash on delivery charge management and COD transfer tickets
- **Webhook Integration** - Parse incoming webhooks with typed resources and event dispatcher
- **Support Tickets** - Create and manage vendor support tickets
- **Staff Management** - List and search vendor staff members
- **Type-Safe** - Immutable resource objects with readonly properties
- **Well Tested** - Comprehensive test suite with mocked HTTP responses
- **PSR-12 Compliant** - Enforced via PHP-CS-Fixer

## Requirements

- PHP 8.1+
- Guzzle 7.0+

## Installation

```bash
composer require pralhadstha/nepal-can-php-sdk
```

## Configuration

```php
use OmniCargo\NepalCan\Client;
use OmniCargo\NepalCan\Config;

// Sandbox environment (default)
$client = new Client('your-api-token');

// Production environment
$client = new Client('your-api-token', Config::BASE_URL_PRODUCTION);
```

> **Note:** Request your API token from NCM's IT Admin. Sandbox and production environments use separate tokens.

## Usage

### Branches

```php
$branches = $client->branches->list();

foreach ($branches as $branch) {
    echo $branch->name;
    echo $branch->district;
}
```

### Shipping Rates

```php
use OmniCargo\NepalCan\Services\RateService;

$rate = $client->rates->calculate('TINKUNE', 'BIRATNAGAR', RateService::TYPE_PICKUP_COLLECT);

echo $rate->charge;
```

Available delivery types:
- `RateService::TYPE_PICKUP_COLLECT` - Door2Door (NCM pickup & delivery)
- `RateService::TYPE_SEND` - Branch2Door (Sender drops at branch, NCM delivers)
- `RateService::TYPE_D2B` - Door2Branch (NCM picks, customer collects at branch)
- `RateService::TYPE_B2B` - Branch2Branch (Sender drops at branch, customer collects)

### Create Order

```php
$order = $client->shipments->create([
    'name' => 'John Doe',
    'phone' => '9847023226',
    'cod_charge' => '2200',
    'address' => 'Byas Pokhari',
    'fbranch' => 'TINKUNE',
    'branch' => 'BIRATNAGAR',
    'package' => 'Jeans Pant',
    'vref_id' => 'VREF234',
    'instruction' => 'Handle with care',
    'delivery_type' => 'Branch2Door',
    'weight' => '2',
]);

echo $order->orderId;
```

### Get Order Details

```php
$order = $client->shipments->find(134);

echo $order->codCharge;
echo $order->deliveryCharge;
echo $order->lastDeliveryStatus;
echo $order->paymentStatus;
```

### Order Comments

```php
// Get comments for an order
$comments = $client->shipments->getComments(134);

foreach ($comments as $comment) {
    echo $comment->comments;
    echo $comment->addedBy;
}

// Get last 25 bulk comments
$comments = $client->shipments->getBulkComments();

// Add a comment
$client->shipments->addComment(1234567, 'Test comment from api');
```

### Tracking

```php
// Get status history for an order
$statuses = $client->tracking->getStatusHistory(134);

foreach ($statuses as $status) {
    echo $status->status;
    echo $status->addedTime;
}

// Bulk status check
$result = $client->tracking->getBulkStatuses([4041, 3855, 4032]);

// $result['result'] => ['4041' => 'Pickup Order Created', ...]
// $result['errors'] => [4042, ...] (invalid order IDs)
```

### Returns & Exchanges

```php
// Return an order
$client->shipments->returnOrder(4041, 'Customer refused the delivery');

// Create exchange order
$response = $client->shipments->createExchange(4041);
// $response['cust_order'] => new delivery order ID
// $response['ven_order'] => return order ID

// Redirect an order
$client->shipments->redirect(4041, [
    'name' => 'Jane Doe',
    'phone' => '9800000000',
    'address' => 'New Address',
]);
```

### Tickets

```php
use OmniCargo\NepalCan\Services\TicketService;

// Create a general ticket
$ticket = $client->tickets->create(TicketService::TYPE_GENERAL, 'Please arrange delivery at the earliest');

echo $ticket->ticketId;

// Create COD transfer ticket
$ticket = $client->tickets->createCodTransfer('Nepal Bank Limited', 'John Doe', '1234567890');

// Close a ticket
$client->tickets->close(123);
```

Available ticket types: `General`, `Order Processing`, `Return`, `Pickup`

### Staff

```php
$result = $client->staff->list(search: 'Ram', page: 1, pageSize: 20);

echo $result['count'];

foreach ($result['results'] as $staff) {
    echo $staff->name;
    echo $staff->email;
    echo $staff->phone;
}
```

## Webhooks

NCM sends HTTP POST requests to your configured webhook URL when order status changes occur. This SDK provides typed parsing via `$client->webhooks->parse()`, User-Agent validation via `$client->webhooks->isValidUserAgent()`, and an event dispatcher for clean webhook handling.

### Event Dispatcher

Use the `EventDispatcher` to route webhook events to handler classes instead of if/else chains:

```php
use OmniCargo\NepalCan\Webhooks\EventDispatcher;
use OmniCargo\NepalCan\Webhooks\WebhookEvent;
use OmniCargo\NepalCan\Webhooks\WebhookHandlerInterface;
use OmniCargo\NepalCan\Resources\Webhook;

// Create a handler
class DeliveryCompletedHandler implements WebhookHandlerInterface
{
    public function handle(Webhook $webhook): void
    {
        // Update order status in your system
    }
}

// Register handlers and dispatch
$dispatcher = new EventDispatcher();
$dispatcher
    ->subscribe(WebhookEvent::DELIVERY_COMPLETED, new DeliveryCompletedHandler())
    ->subscribe(WebhookEvent::ORDER_DISPATCHED, new OrderDispatchedHandler());

$webhook = $client->webhooks->parse($payload);
$dispatcher->dispatch($webhook);
```

### Laravel Example

A complete example of handling NCM webhooks in a Laravel application:

```php
// routes/api.php
use Illuminate\Http\Request;
use OmniCargo\NepalCan\Client;
use OmniCargo\NepalCan\Webhooks\EventDispatcher;
use OmniCargo\NepalCan\Webhooks\WebhookEvent;

Route::post('/webhooks/ncm', function (Request $request) {
    $client = new Client(config('services.ncm.token'));

    // Validate the request is from NCM
    if (!$client->webhooks->isValidUserAgent($request->userAgent())) {
        abort(403, 'Invalid webhook source');
    }

    // Parse the webhook payload
    $webhook = $client->webhooks->parse($request->getContent());

    // Handle test webhooks
    if ($webhook->isTest) {
        return response()->json(['status' => 'test received']);
    }

    // Dispatch to handler classes
    $dispatcher = new EventDispatcher();
    $dispatcher
        ->subscribe(WebhookEvent::DELIVERY_COMPLETED, new DeliveryCompletedHandler())
        ->subscribe(WebhookEvent::ORDER_DISPATCHED, new OrderDispatchedHandler())
        ->subscribe(WebhookEvent::PICKUP_COMPLETED, new PickupCompletedHandler());

    $dispatcher->dispatch($webhook);

    return response()->json(['status' => 'received']);
});
```

```php
// app/Webhooks/DeliveryCompletedHandler.php
use OmniCargo\NepalCan\Resources\Webhook;
use OmniCargo\NepalCan\Webhooks\WebhookHandlerInterface;

class DeliveryCompletedHandler implements WebhookHandlerInterface
{
    public function handle(Webhook $webhook): void
    {
        Order::where('ncm_order_id', $webhook->orderId)
            ->update(['status' => $webhook->status, 'delivered_at' => now()]);
    }
}
```

Supported webhook events:
- `WebhookEvent::PICKUP_COMPLETED` - Order picked up
- `WebhookEvent::SENT_FOR_DELIVERY` - Order sent for delivery
- `WebhookEvent::ORDER_DISPATCHED` - Order dispatched from origin branch
- `WebhookEvent::ORDER_ARRIVED` - Order arrived at destination branch
- `WebhookEvent::DELIVERY_COMPLETED` - Order delivered

### Idempotency

NCM may send duplicate webhook notifications. Your application should handle this by tracking processed webhooks to avoid processing the same event twice:

```php
class DeliveryCompletedHandler implements WebhookHandlerInterface
{
    public function handle(Webhook $webhook): void
    {
        // Create a unique key from the webhook data
        $idempotencyKey = $webhook->orderId . ':' . $webhook->event . ':' . $webhook->timestamp;

        // Skip if already processed
        if (ProcessedWebhook::where('idempotency_key', $idempotencyKey)->exists()) {
            return;
        }

        // Process the webhook
        Order::where('ncm_order_id', $webhook->orderId)
            ->update(['status' => $webhook->status]);

        // Mark as processed
        ProcessedWebhook::create(['idempotency_key' => $idempotencyKey]);
    }
}
```

## Error Handling

```php
use OmniCargo\NepalCan\Exceptions\ApiException;
use OmniCargo\NepalCan\Exceptions\AuthenticationException;
use OmniCargo\NepalCan\Exceptions\ValidationException;
use OmniCargo\NepalCan\Exceptions\NotFoundException;

try {
    $order = $client->shipments->find(99999);
} catch (AuthenticationException $e) {
    // Invalid or missing API token (401)
} catch (ValidationException $e) {
    // Invalid parameters (400)
    $errors = $e->getErrors();
} catch (NotFoundException $e) {
    // Order not found (404)
} catch (ApiException $e) {
    // Other API errors
    $statusCode = $e->getStatusCode();
    $errorBody = $e->getErrorBody();
}
```

## API Limits

- Order Creation: 1,000 per day
- Order View (Detail, Comments, Status): 20,000 per day

## Testing

Run the test suite:

```bash
vendor/bin/phpunit
```

## Code Style

This project follows PSR-12 coding standards enforced via [PHP-CS-Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer).

```bash
# Check for style violations
composer cs-check

# Auto-fix style violations
composer cs-fix
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Credits

- [Pralhad Shrestha](https://github.com/pralhadstha)
- [Nepal Can Move (NCM)](https://nepalcanmove.com) - Shipping & Courier API Provider

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) file for more information.
