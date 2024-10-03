<?php

namespace App\Services;

use App\Interfaces\TeamRepositoryInterface;
use App\Interfaces\SeasonRepositoryInterface;
use App\Interfaces\WeekRepositoryInterface;
use Illuminate\Support\Collection;

class LeagueSimulationService
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
}
