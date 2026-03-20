<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Services;

use OmniCargo\NepalCan\Http\HttpClient;
use OmniCargo\NepalCan\Resources\Staff;
use OmniCargo\NepalCan\Support\Mapper;

final class StaffService
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    public function list(?string $search = null, int $page = 1, int $pageSize = 20): array
    {
        $params = [
            'page' => $page,
            'page_size' => $pageSize,
        ];

        if ($search !== null) {
            $params['q'] = $search;
        }

        $response = $this->http->get('/api/v2/vendor/staffs', $params);

        $staffList = Mapper::mapArray(
            $response['results'] ?? [],
            fn (array $data) => Staff::fromArray($data)
        );

        return [
            'count' => $response['count'] ?? 0,
            'next' => $response['next'] ?? null,
            'previous' => $response['previous'] ?? null,
            'results' => $staffList,
        ];
    }
}
