<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Auth;

use OmniCargo\NepalCan\Config;

final class ApiKeyAuthenticator
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getHeaders(): array
    {
        return [
            'Authorization' => 'Token ' . $this->config->getApiToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
