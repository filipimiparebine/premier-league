<?php

namespace App\Services;

use Illuminate\Support\Collection;

class FixtureGenerator
{
    private Collection $teams;
    private int $teamCount;
    private array $teamIds;

    public function generateFixtures(Collection $teams): array
    {
        $this->teams = $teams;
        $this->teamCount = $this->ensureEvenTeamCount();
        $this->teamIds = $this->teams->pluck('id')->toArray();

        return $this->createSchedule();
    }

    private function ensureEvenTeamCount(): int
    {
        $count = $this->teams->count();
        if ($count % 2 !== 0) {
            $this->teams->push(null);
            $count++;
        }
        return $count;
    }

    private function createSchedule(): array
    {
        $rounds = ($this->teamCount - 1) * 2; // Home and away matches
        $matchesPerRound = $this->teamCount / 2;
        $fixtures = [];

        for ($round = 0; $round < $rounds; $round++) {
            $fixtures[] = $this->generateRoundMatches($round, $matchesPerRound);
            $this->rotateTeams();
        }

        return $fixtures;
    }

    private function generateRoundMatches(int $round, int $matchesPerRound): array
    {
        $roundMatches = [];
        $isSecondHalf = $round >= ($this->teamCount - 1);

        for ($match = 0; $match < $matchesPerRound; $match++) {
            $homeIndex = $match;
            $awayIndex = $this->teamCount - 1 - $match;

            if ($this->isValidMatch($homeIndex, $awayIndex)) {
                $roundMatches[] = $this->createMatch($homeIndex, $awayIndex, $isSecondHalf);
            }
        }

        return $roundMatches;
    }

    private function isValidMatch(int $homeIndex, int $awayIndex): bool
    {
        return $homeIndex < count($this->teamIds)
            && $awayIndex < count($this->teamIds)
            && $this->teamIds[$homeIndex] !== null
            && $this->teamIds[$awayIndex] !== null;
    }

    private function createMatch(int $homeIndex, int $awayIndex, bool $isSecondHalf): array
    {
        if ($isSecondHalf) {
            return [
                'home' => $this->teamIds[$awayIndex],
                'away' => $this->teamIds[$homeIndex]
            ];
        }

        return [
            'home' => $this->teamIds[$homeIndex],
            'away' => $this->teamIds[$awayIndex]
        ];
    }

    private function rotateTeams(): void
    {
        array_splice($this->teamIds, 1, 0, array_pop($this->teamIds));
    }
}
