<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Team;
use App\Models\Season;
use Database\Seeders\DatabaseSeeder;
use App\Interfaces\WeekRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoutesTest extends TestCase
{
    use RefreshDatabase;

    private $weekRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
        $this->weekRepository = resolve(WeekRepositoryInterface::class);
    }

    public function testStartSeason()
    {
        $teams = Team::pluck('id')->take(4)->toArray();
        $season = Season::first();
        $res = $this->postJson('api/start-season', [
            'team_ids' => $teams,
            'season_id' => $season->id
        ]);
        $this->assertEquals('Season started successfully', json_decode($res->getContent())->message);

        $lastWeek = $this->weekRepository->all()->last();
        $this->assertEquals(6, $lastWeek->week_number);
    }

    public function testStartSeasonWith10Teams()
    {
        $teams = Team::pluck('id')->take(10)->toArray();
        $season = Season::first();
        $res = $this->postJson('api/start-season', [
            'team_ids' => $teams,
            'season_id' => $season->id
        ]);
        $this->assertEquals('Season started successfully', json_decode($res->getContent())->message);

        $lastWeek = $this->weekRepository->all()->last();
        $this->assertEquals(18, $lastWeek->week_number);
    }

    public function testGetLeagueTable()
    {
        $teams = Team::pluck('id')->take(10)->toArray();
        $season = Season::first();
        $res = $this->postJson('api/start-season', [
            'team_ids' => $teams,
            'season_id' => $season->id
        ]);
        $res = $this->get("api/league-table/{$season->id}");

        $response = json_decode($res->getContent());
        $this->assertEquals(10, count($response->leaderboard));
    }
}
