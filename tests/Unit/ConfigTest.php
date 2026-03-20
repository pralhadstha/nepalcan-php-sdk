<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Tests\Unit;

use OmniCargo\NepalCan\Config;
use OmniCargo\NepalCan\Tests\TestCase;

final class ConfigTest extends TestCase
{
    public function test_config_with_valid_token(): void
    {
        $config = new Config('my-api-token');

        $this->assertEquals('my-api-token', $config->getApiToken());
    }

    public function test_config_with_empty_token_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Config('');
    }

    public function test_config_defaults_to_sandbox(): void
    {
        $config = new Config('my-api-token');

        $this->assertEquals(Config::BASE_URL_SANDBOX, $config->getBaseUrl());
    }

    public function test_config_with_production_url(): void
    {
        $config = new Config('my-api-token', Config::BASE_URL_PRODUCTION);

        $this->assertEquals(Config::BASE_URL_PRODUCTION, $config->getBaseUrl());
    }

    public function test_config_trims_trailing_slash(): void
    {
        $config = new Config('my-api-token', 'https://example.com/');

        $this->assertEquals('https://example.com', $config->getBaseUrl());
    }
}
