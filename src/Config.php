<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan;

final class Config
{
    public const BASE_URL_PRODUCTION = 'https://nepalcanmove.com';
    public const BASE_URL_SANDBOX = 'https://demo.nepalcanmove.com';

    private string $apiToken;
    private string $baseUrl;

    public function __construct(string $apiToken, string $baseUrl = self::BASE_URL_SANDBOX)
    {
        if (empty($apiToken)) {
            throw new \InvalidArgumentException('API token cannot be empty');
        }

        $this->apiToken = $apiToken;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function getApiToken(): string
    {
        return $this->apiToken;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
