# Nepal Can Move (NCM) PHP SDK

A PHP SDK for integrating with the Nepal Can Move shipping API.

## Requirements

- PHP 8.1+
- Guzzle 7.0+

## Installation

```bash
composer require pralhadstha/nepal-can-php-sdk
```

## Quick Start

```php
use OmniCargo\NepalCan\Client;
use OmniCargo\NepalCan\Config;

// Sandbox (default)
$client = new Client('your-api-token');

// Production
$client = new Client('your-api-token', Config::BASE_URL_PRODUCTION);
```

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

### Webhooks

```php
// Parse incoming webhook payload
$payload = file_get_contents('php://input');
$webhook = $client->webhooks->parse($payload);

if ($webhook->isTest) {
    // Handle test webhook
}

echo $webhook->event;    // e.g., 'delivery_completed'
echo $webhook->status;   // e.g., 'Delivered'
echo $webhook->orderId;  // Single order
echo $webhook->orderIds; // Bulk orders (array)

// Validate User-Agent
$isValid = $client->webhooks->isValidUserAgent($_SERVER['HTTP_USER_AGENT'] ?? '');
```

Supported webhook events:
- `pickup_completed` - Order picked up
- `sent_for_delivery` - Order sent for delivery
- `order_dispatched` - Order dispatched from origin branch
- `order_arrived` - Order arrived at destination branch
- `delivery_completed` - Order delivered

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

## License

MIT
