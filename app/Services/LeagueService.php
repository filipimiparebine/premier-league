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

        $fixtures = $this->createFixtures($teams);

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

    private function createFixtures(Collection $teams): array
    {
        $teamCount = $teams->count();
        // Ensure there is an even number of teams by adding a dummy team if necessary.
        if ($teamCount % 2 !== 0) {
            $teams->push(null); // Add a dummy team (null) to make the count even.
            $teamCount++;
        }
        $rounds = ($teamCount - 1) * 2;  // Each team plays home and away, so double the rounds.
        $matchesPerRound = $teamCount / 2;

        $teamIds = $teams->pluck('id')->toArray();

        $fixtures = [];
        // Generate fixtures for each round
        for ($round = 0; $round < $rounds; $round++) {
            $roundMatches = [];
            // Generate matches for the current round
            for ($match = 0; $match < $matchesPerRound; $match++) {
                $homeTeam = $teamIds[$match];
                $awayTeam = $teamIds[$teamCount - 1 - $match];
                // Skip matches involving the dummy team
                if ($homeTeam === null || $awayTeam === null) {
                    continue;
                }
                // In the first half of the rounds, the home team is fixed, in the second half, it's reversed.
                $roundMatches[] = ($round < $rounds / 2)
                    ? ['home' => $homeTeam, 'away' => $awayTeam]
                    : ['home' => $awayTeam, 'away' => $homeTeam];
            }

            $fixtures[] = $roundMatches;

            // Rotate the teams for the next round (except for the first team)
            array_splice($teamIds, 1, 0, array_pop($teamIds));
        }

        return $fixtures;
    }

    public function simulateWeek(int $seasonId, int $weekNumber): void
    {
        $weekMatches = $this->weekRepository->getWeek($seasonId, $weekNumber);

        foreach ($weekMatches as $weekMatch) {
            $homeScore = rand(0, 5);
            $awayScore = rand(0, 5);

            $this->weekRepository->updateMatchResult($weekMatch->id, $homeScore, $awayScore);
            $this->updateTeamStats($weekMatch, $homeScore, $awayScore);
        }
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
}
