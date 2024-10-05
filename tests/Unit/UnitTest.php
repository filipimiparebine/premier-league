<?php

namespace Tests\Unit;

use App\Interfaces\LeagueServiceInterface;
use Tests\TestCase;
use App\Models\Team;
use App\Models\Week;
use App\Models\Season;
use Database\Seeders\DatabaseSeeder;
use App\Interfaces\TeamRepositoryInterface;
use App\Interfaces\WeekRepositoryInterface;
use App\Interfaces\SeasonRepositoryInterface;
use App\Models\SeasonLeaderboard;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UnitTest extends TestCase
{
    use RefreshDatabase;

    private $teamRepository;
    private $weekRepository;
    private $seasonRepository;
    private $leagueService;

    const HOME_SCORE = 5;
    const AWAY_SCORE = 3;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $this->teamRepository = resolve(TeamRepositoryInterface::class);
        $this->weekRepository = resolve(WeekRepositoryInterface::class);
        $this->seasonRepository = resolve(SeasonRepositoryInterface::class);
        $this->leagueService = resolve(LeagueServiceInterface::class);
    }

    public function testGetTeams()
    {
        $teams = $this->teamRepository->all();
        $this->assertCount(20, $teams);
    }

    public function testScoreLeaderboards()
    {
        $season = Season::first();
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();
        $match = Week::create([
            'season_id' => $season->id,
            'week_number' => 1,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'home_score' => self::HOME_SCORE,
            'away_score' => self::AWAY_SCORE,
        ]);
        $this->seasonRepository->fillLeagueTable([$homeTeam->id, $awayTeam->id], $season->id);
        $this->weekRepository->updateMatchResult($match->id, self::HOME_SCORE, self::AWAY_SCORE);

        $match->refresh();
        $this->assertEquals(self::HOME_SCORE, $match->home_score);
        $this->assertEquals(self::AWAY_SCORE, $match->away_score);

        $this->leagueService->updateTeamStats($match, self::HOME_SCORE, self::AWAY_SCORE);
        $season->refresh();

        $homeTeamStats = $season->leaderboard->where('team_id', $homeTeam->id)->first();
        $awayTeamStats = $season->leaderboard->where('team_id', $awayTeam->id)->first();

        // HOME WIN
        $this->assertWinningTeamStats($homeTeamStats);
        $this->assertLosingTeamStats($awayTeamStats);

        $this->leagueService->updateMatchResult($match->id, self::AWAY_SCORE, self::HOME_SCORE);

        $match->refresh();
        $this->assertEquals(self::AWAY_SCORE, $match->home_score);
        $this->assertEquals(self::HOME_SCORE, $match->away_score);

        $homeTeamStats->refresh();
        $awayTeamStats->refresh();

        // AWAY WIN
        $this->assertWinningTeamStats($awayTeamStats);
        $this->assertLosingTeamStats($homeTeamStats);

        $this->leagueService->updateMatchResult($match->id, self::HOME_SCORE, self::HOME_SCORE);

        $match->refresh();
        $this->assertEquals(self::HOME_SCORE, $match->home_score);
        $this->assertEquals(self::HOME_SCORE, $match->away_score);

        $homeTeamStats->refresh();
        $awayTeamStats->refresh();

        // DRAW
        $this->assertDrawTeamStats($awayTeamStats);
        $this->assertDrawTeamStats($homeTeamStats);
    }

    private function assertWinningTeamStats(SeasonLeaderboard $teamStats)
    {
        $this->assertEquals(3, $teamStats->points);
        $this->assertEquals(1, $teamStats->played_matches);
        $this->assertEquals(1, $teamStats->won);
        $this->assertEquals(0, $teamStats->drawn);
        $this->assertEquals(0, $teamStats->lost);
        $this->assertEquals(abs(self::HOME_SCORE - self::AWAY_SCORE), $teamStats->goal_difference);
    }

    private function assertLosingTeamStats(SeasonLeaderboard $teamStats)
    {
        $this->assertEquals(0, $teamStats->points);
        $this->assertEquals(1, $teamStats->played_matches);
        $this->assertEquals(0, $teamStats->won);
        $this->assertEquals(0, $teamStats->drawn);
        $this->assertEquals(1, $teamStats->lost);
        $this->assertEquals(- (abs(self::HOME_SCORE - self::AWAY_SCORE)), $teamStats->goal_difference);
    }

    private function assertDrawTeamStats(SeasonLeaderboard $teamStats)
    {
        $this->assertEquals(1, $teamStats->points);
        $this->assertEquals(1, $teamStats->played_matches);
        $this->assertEquals(0, $teamStats->won);
        $this->assertEquals(1, $teamStats->drawn);
        $this->assertEquals(0, $teamStats->lost);
        $this->assertEquals(0, $teamStats->goal_difference);
    }
}
