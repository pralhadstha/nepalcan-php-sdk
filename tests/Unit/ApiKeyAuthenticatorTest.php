<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Tests\Unit;

use OmniCargo\NepalCan\Auth\ApiKeyAuthenticator;
use OmniCargo\NepalCan\Config;
use OmniCargo\NepalCan\Tests\TestCase;

final class ApiKeyAuthenticatorTest extends TestCase
{
    public function test_get_headers_returns_correct_format(): void
    {
        $config = new Config('my-secret-token');
        $auth = new ApiKeyAuthenticator($config);

        $headers = $auth->getHeaders();

        $this->assertEquals('Token my-secret-token', $headers['Authorization']);
        $this->assertEquals('application/json', $headers['Content-Type']);
        $this->assertEquals('application/json', $headers['Accept']);
    }
}
