<?php

namespace App\Services;

use App\Models\Week;
use Illuminate\Support\Collection;
use App\Interfaces\TeamRepositoryInterface;
use App\Interfaces\WeekRepositoryInterface;
use App\Interfaces\SeasonRepositoryInterface;
use App\Models\SeasonLeaderboard;

class LeagueService
{
    private const WEIGHTS = [
        'POINTS' => 0.4,
        'WINS' => 0.3,
        'GOALS' => 0.2,
        'HOME' => 0.1
    ];
    public function __construct(
        private TeamRepositoryInterface $teamRepository,
        private SeasonRepositoryInterface $seasonRepository,
        private WeekRepositoryInterface $weekRepository
    ) {}

    public function generateFixtures(array $teamIds, int $seasonId): void
    {
        $teams = $this->teamRepository->all()->whereIn('id', $teamIds);
        $season = $this->seasonRepository->find($seasonId);

        $existingFixtures = $this->weekRepository->all()->where('season_id', $seasonId);
        if ($existingFixtures) {
            $existingFixtures->each(fn($week) => $week->delete());
        }

        $generator = new FixtureGenerator();
        $fixtures = $generator->generateFixtures($teams);

        foreach ($fixtures as $weekNumber => $matches) {
            foreach ($matches as $match) {
                $this->weekRepository->create([
                    'season_id' => $season->id,
                    'week_number' => $weekNumber + 1,
                    'home_team_id' => $match['home'],
                    'away_team_id' => $match['away'],
                ]);
            }
        }
    }

    public function simulateWeek(int $seasonId, int $weekNumber): void
    {
        $weekMatches = $this->weekRepository->getWeek($seasonId, $weekNumber);

        foreach ($weekMatches as $weekMatch) {
            $homeTeamStats = SeasonLeaderboard::whereSeasonId($seasonId)->whereTeamId($weekMatch->home_team_id)->first();
            $awayTeamStats = SeasonLeaderboard::whereSeasonId($seasonId)->whereTeamId($weekMatch->away_team_id)->first();

            $winDiff = $homeTeamStats->won - $awayTeamStats->won;
            list($homeScore, $awayScore) = $this->scoreFactorWin($winDiff);

            $this->weekRepository->updateMatchResult($weekMatch->id, $homeScore, $awayScore);
            $this->updateTeamStats($weekMatch, $homeScore, $awayScore);
        }
    }

    private function scoreFactorWin(int $winDiff)
    {
        $maxGoals = 9;
        $homeScore = rand(0, $maxGoals);
        $awayScore = rand(0, $maxGoals);

        if ($winDiff > 0) {
            $homeScore += $this->boostBasedOnWins($winDiff);
        } elseif ($winDiff < 0) {
            $awayScore += $this->boostBasedOnWins($winDiff);
        }

        $homeScore = min($homeScore, $maxGoals);
        $awayScore = min($awayScore, $maxGoals);

        return [$homeScore, $awayScore];
    }

    private function boostBasedOnWins($winDiff)
    {
        $absWinDiff = abs($winDiff);
        if ($absWinDiff > 5) {
            return 3;
        }
        if ($absWinDiff >= 4) {
            return 2;
        }
        if ($absWinDiff >= 2) {
            return 1;
        }
        return 0;
    }

    public function updateTeamStats(Week $weekMatch, int $homeScore, int $awayScore, int $oldHomeScore = null, int $oldAwayScore = null): void
    {
        $currentHomeStats = SeasonLeaderboard::whereSeasonId($weekMatch->season_id)->whereTeamId($weekMatch->home_team_id)->first();
        $currentAwayStats = SeasonLeaderboard::whereSeasonId($weekMatch->season_id)->whereTeamId($weekMatch->away_team_id)->first();

        if (!is_null($oldHomeScore) && !is_null($oldAwayScore)) {
            $this->revertMatchStats($currentHomeStats, $oldHomeScore, $oldAwayScore);
            $this->revertMatchStats($currentAwayStats, $oldAwayScore, $oldHomeScore);
        }

        $homeStats = $this->calculateStats($currentHomeStats, $homeScore, $awayScore);
        $awayStats = $this->calculateStats($currentAwayStats, $awayScore, $homeScore);

        $this->seasonRepository->updateTeamStats($weekMatch->season_id, $weekMatch->home_team_id, $homeStats);
        $this->seasonRepository->updateTeamStats($weekMatch->season_id, $weekMatch->away_team_id, $awayStats);
    }

    private function calculateStats(SeasonLeaderboard $currentStats, int $teamScore, int $opponentScore): array
    {
        $currentStats->refresh();

        $stats = [
            'played_matches' => $currentStats->played_matches + 1,
            'goal_difference' => $currentStats->goal_difference + ($teamScore - $opponentScore),
        ];

        if ($teamScore > $opponentScore) {
            $stats['won'] = $currentStats->won + 1;
            $stats['points'] = $currentStats->points + 3;
        } elseif ($teamScore < $opponentScore) {
            $stats['lost'] = $currentStats->lost + 1;
        } else {
            $stats['drawn'] = $currentStats->drawn + 1;
            $stats['points'] = $currentStats->points + 1;
        }

        return $stats;
    }

    private function revertMatchStats(SeasonLeaderboard &$stats, int $teamScore, int $opponentScore)
    {
        $stats->played_matches -= 1;
        $stats->goal_difference -= $teamScore - $opponentScore;

        if ($teamScore > $opponentScore) {
            $stats->won -= 1;
            $stats->points -= 3;
        } elseif ($teamScore < $opponentScore) {
            $stats->lost -= 1;
        } else {
            $stats->drawn -= 1;
            $stats->points -= 1;
        }
        $stats->save();
    }

    public function updateMatchResult(int $matchId, int $homeScore, int $awayScore): void
    {
        $match = $this->weekRepository->find($matchId);
        if (!$match) {
            return;
        }

        $oldHomeScore = $match->home_score;
        $oldAwayScore = $match->away_score;

        $this->weekRepository->updateMatchResult($matchId, $homeScore, $awayScore);
        $this->updateTeamStats($match, $homeScore, $awayScore, $oldHomeScore, $oldAwayScore);
    }

    public function predictWeek(int $seasonId, int $weekNumber)
    {
        $weekMatches = $this->weekRepository->getWeek($seasonId, $weekNumber);

        $predictions = collect();
        foreach ($weekMatches as $weekMatch) {
            $homeTeamStats = SeasonLeaderboard::whereSeasonId($seasonId)->whereTeamId($weekMatch->home_team_id)->first();
            $awayTeamStats = SeasonLeaderboard::whereSeasonId($seasonId)->whereTeamId($weekMatch->away_team_id)->first();

            $predictions->add([
                'team' => $weekMatch->homeTeam,
                'points' => $this->teamPredictPoints($homeTeamStats, true)
            ]);
            $predictions->add([
                'team' => $weekMatch->awayTeam,
                'points' => $this->teamPredictPoints($awayTeamStats)
            ]);
        }

        $totalPoints = $predictions->sum('points');

        $result = $predictions->map(function ($prediction) use ($totalPoints) {
            $percentage = ($prediction['points'] / $totalPoints) * 100;
            return [
                'team' => $prediction['team'],
                'prediction' => round($percentage)
            ];
        });

        // Correct rounding error
        $firstItem = $result->first();
        $predictionSum = $result->sum('prediction');
        $firstItem['prediction'] += 100 - $predictionSum;
        $result = $result->splice(1)->prepend($firstItem);

        return $result->sortByDesc('prediction');
    }

    private function teamPredictPoints(SeasonLeaderboard $teamStats, bool $home = false): float
    {
        $points = array_sum([
            self::WEIGHTS['POINTS'] * max(0, $teamStats->points),
            self::WEIGHTS['WINS'] * max(0, $teamStats->won),
            self::WEIGHTS['GOALS'] * max(0, $teamStats->goal_difference),
        ]);
        if ($home) {
            return self::WEIGHTS['HOME'] * $points;
        }
        return $points;
    }
}
