<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Services;

use OmniCargo\NepalCan\Http\HttpClient;
use OmniCargo\NepalCan\Resources\Branch;
use OmniCargo\NepalCan\Support\Mapper;

final class BranchService
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /** @return Branch[] */
    public function list(): array
    {
        $response = $this->http->get('/api/v2/branches');

        return Mapper::mapArray($response, fn(array $data) => Branch::fromArray($data));
    }
}
