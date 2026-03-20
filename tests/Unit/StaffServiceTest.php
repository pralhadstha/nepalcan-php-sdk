<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Tests\Unit;

use OmniCargo\NepalCan\Resources\Staff;
use OmniCargo\NepalCan\Services\StaffService;
use OmniCargo\NepalCan\Tests\TestCase;

final class StaffServiceTest extends TestCase
{
    public function test_list_staff_success(): void
    {
        $fixture = $this->loadFixture('staff_list_success.json');
        $http = $this->mockHttpClient($fixture);

        $service = new StaffService($http);
        $result = $service->list();

        $this->assertEquals(45, $result['count']);
        $this->assertNotNull($result['next']);
        $this->assertNull($result['previous']);
        $this->assertCount(2, $result['results']);
        $this->assertInstanceOf(Staff::class, $result['results'][0]);
        $this->assertEquals('Ram Sharma', $result['results'][0]->name);
        $this->assertEquals('ram@example.com', $result['results'][0]->email);
        $this->assertEquals('9841234567', $result['results'][0]->phone);
    }

    public function test_list_staff_with_search(): void
    {
        $fixture = $this->loadFixture('staff_list_success.json');
        $http = $this->mockHttpClient($fixture);

        $service = new StaffService($http);
        $result = $service->list(search: 'Ram', page: 1, pageSize: 10);

        $this->assertArrayHasKey('count', $result);
        $this->assertArrayHasKey('results', $result);
        $this->assertNotEmpty($result['results']);
    }
}
