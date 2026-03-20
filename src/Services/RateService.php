<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Services;

use OmniCargo\NepalCan\Http\HttpClient;
use OmniCargo\NepalCan\Resources\Rate;
use OmniCargo\NepalCan\Support\Mapper;

final class RateService
{
    public const TYPE_PICKUP_COLLECT = 'Pickup/Collect';
    public const TYPE_SEND = 'Send';
    public const TYPE_D2B = 'D2B';
    public const TYPE_B2B = 'B2B';

    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    public function calculate(string $origin, string $destination, string $type = self::TYPE_PICKUP_COLLECT): Rate
    {
        $response = $this->http->get('/api/v1/shipping-rate', [
            'creation' => $origin,
            'destination' => $destination,
            'type' => $type,
        ]);

        return Mapper::mapSingle($response, fn (array $data) => Rate::fromArray($data, $origin, $destination, $type));
    }
}
