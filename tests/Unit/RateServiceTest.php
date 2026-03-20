<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Tests\Unit;

use OmniCargo\NepalCan\Resources\Rate;
use OmniCargo\NepalCan\Services\RateService;
use OmniCargo\NepalCan\Tests\TestCase;

final class RateServiceTest extends TestCase
{
    public function test_calculate_rate_success(): void
    {
        $fixture = $this->loadFixture('rate_success.json');
        $http = $this->mockHttpClient($fixture);

        $service = new RateService($http);
        $rate = $service->calculate('TINKUNE', 'BIRATNAGAR', RateService::TYPE_PICKUP_COLLECT);

        $this->assertEquals('99.00', $rate->charge);
        $this->assertEquals('TINKUNE', $rate->origin);
        $this->assertEquals('BIRATNAGAR', $rate->destination);
        $this->assertEquals(RateService::TYPE_PICKUP_COLLECT, $rate->type);
    }

    public function test_calculate_rate_returns_rate_resource(): void
    {
        $fixture = $this->loadFixture('rate_success.json');
        $http = $this->mockHttpClient($fixture);

        $service = new RateService($http);
        $rate = $service->calculate('TINKUNE', 'BIRATNAGAR');

        $this->assertInstanceOf(Rate::class, $rate);
        $this->assertNotEmpty($rate->rawData);
    }
}
