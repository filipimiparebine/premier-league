<?php

namespace App\Interfaces;

use App\Models\Week;

interface LeagueServiceInterface
{
    public function generateFixtures(array $teamIds, int $seasonId): void;
    public function simulateWeek(int $seasonId, int $weekNumber): void;
    public function updateTeamStats(Week $weekMatch, int $homeScore, int $awayScore, int $oldHomeScore = null, int $oldAwayScore = null): void;
    public function updateMatchResult(int $matchId, int $homeScore, int $awayScore): void;
    public function predictWeek(int $seasonId, int $weekNumber): array;
}
