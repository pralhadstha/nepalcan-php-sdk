<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Tests\Unit;

use OmniCargo\NepalCan\Exceptions\AuthenticationException;
use OmniCargo\NepalCan\Exceptions\NotFoundException;
use OmniCargo\NepalCan\Exceptions\ValidationException;
use OmniCargo\NepalCan\Tests\TestCase;

final class HttpClientTest extends TestCase
{
    public function test_authentication_exception_on_401(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Authentication credentials were not provided.');

        $fixture = $this->loadFixture('error_401.json');
        $http = $this->mockHttpClient($fixture, 401);

        $http->get('/api/v1/order', ['id' => 1]);
    }

    public function test_validation_exception_on_400(): void
    {
        $this->expectException(ValidationException::class);

        $fixture = $this->loadFixture('error_400_validation.json');
        $http = $this->mockHttpClient($fixture, 400);

        $http->post('/api/v1/order/create', ['name' => '']);
    }

    public function test_not_found_exception_on_404(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Not found.');

        $fixture = $this->loadFixture('error_404.json');
        $http = $this->mockHttpClient($fixture, 404);

        $http->get('/api/v1/order', ['id' => 99999]);
    }
}
