<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Tests\Unit;

use OmniCargo\NepalCan\Resources\Branch;
use OmniCargo\NepalCan\Services\BranchService;
use OmniCargo\NepalCan\Tests\TestCase;

final class BranchServiceTest extends TestCase
{
    public function test_list_branches_success(): void
    {
        $fixture = $this->loadFixture('branches_success.json');
        $http = $this->mockHttpClient($fixture);

        $service = new BranchService($http);
        $branches = $service->list();

        $this->assertCount(2, $branches);
        $this->assertInstanceOf(Branch::class, $branches[0]);
        $this->assertEquals(1, $branches[0]->id);
        $this->assertEquals('TINKUNE', $branches[0]->name);
        $this->assertEquals('Kathmandu', $branches[0]->district);
        $this->assertEquals('Bagmati', $branches[0]->region);
        $this->assertNotEmpty($branches[0]->coveredAreas);
        $this->assertEquals('BIRATNAGAR', $branches[1]->name);
    }
}
