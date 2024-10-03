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
        $this->postJson('api/start-season', [
            'team_ids' => $teams,
            'season_id' => $season->id
        ]);
        $res = $this->get("api/league-table/{$season->id}");

        $response = json_decode($res->getContent());
        $this->assertEquals(10, count($response->leaderboard));
    }

    public function testWeekFixtures()
    {
        $teams = Team::pluck('id')->take(10)->toArray();
        $season = Season::first();
        $this->postJson('api/start-season', [
            'team_ids' => $teams,
            'season_id' => $season->id
        ]);
        $res = $this->get("api/fixtures/{$season->id}/1");

        $response = json_decode($res->getContent());
        $this->assertEquals(5, count($response));
    }

    public function testSimulateWeek()
    {
        $teams = Team::pluck('id')->take(10)->toArray();
        $season = Season::first();
        $this->postJson('api/start-season', [
            'team_ids' => $teams,
            'season_id' => $season->id
        ]);
        $res = $this->get("api/simulate-week/{$season->id}/1");
        $response = json_decode($res->getContent());
        $this->assertEquals('Week simulated successfully', $response->message);

        $res = $this->get("api/league-table/{$season->id}");

        $response = json_decode($res->getContent());
        $firstTeam = $response->leaderboard[0];
        $this->assertEquals(3, $firstTeam->points);
        $this->assertEquals(1, $firstTeam->won);
        $this->assertEquals(1, $firstTeam->played_matches);

        $res = $this->get("api/fixtures/{$season->id}/1");

        $response = json_decode($res->getContent());
        $match = collect($response)
            ->first(fn($match) => $match->home_team_id == $firstTeam->team_id || $match->away_team_id == $firstTeam->team_id);

        $this->assertNotNull($match);

        $winningTeamId = $match->home_score > $match->away_score
            ? $match->home_team_id
            : ($match->away_score > $match->home_score ? $match->away_team_id : null);

        $this->assertEquals($firstTeam->team_id, $winningTeamId,);
    }
}
