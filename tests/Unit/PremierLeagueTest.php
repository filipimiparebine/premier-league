<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Interfaces\TeamRepositoryInterface;
use Database\Seeders\TeamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PremierLeagueTest extends TestCase
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
