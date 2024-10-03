<?php

namespace Tests\Unit;

use Tests\TestCase;
use Database\Seeders\TeamSeeder;
use App\Interfaces\TeamRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UnitTest extends TestCase
{
    use RefreshDatabase;

    private $teamRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TeamSeeder::class);

        $this->teamRepository = resolve(TeamRepositoryInterface::class);
    }

    public function testGetTeams()
    {
        $teams = $this->teamRepository->all();
        $this->assertCount(20, $teams);
    }
}
