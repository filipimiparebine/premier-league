<?php

namespace App\Repositories;

use App\Models\Week;
use App\Models\Season;
use App\Models\SeasonLeaderboard;
use Illuminate\Database\Eloquent\Collection;
use App\Interfaces\SeasonRepositoryInterface;

class SeasonRepository implements SeasonRepositoryInterface
{
    public function all(): Collection
    {
        return Season::all();
    }

    public function find(int $id): ?Season
    {
        return Season::find($id);
    }

    public function create(array $data): Season
    {
        return Season::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $season = $this->find($id);
        if (!$season) {
            return false;
        }
        return $season->update($data);
    }

    public function delete(int $id): bool
    {
        $season = $this->find($id);
        if (!$season) {
            return false;
        }
        return $season->delete();
    }

    public function updateTeamStats(int $seasonId, int $teamId, array $stats): bool
    {
        $season = SeasonLeaderboard::where('season_id', $seasonId)
            ->where('team_id', $teamId)
            ->first();

        if (!$season) {
            return false;
        }

        return $season->update($stats);
    }

    public function fillLeagueTable(array $teamIds, int $seasonId): void
    {
        SeasonLeaderboard::whereSeasonId($seasonId)->delete();
        Week::whereSeasonId($seasonId)->delete();
        foreach ($teamIds as $teamId) {
            SeasonLeaderboard::create([
                'season_id' => $seasonId,
                'team_id' => $teamId,
                'points' => 0,
                'played_matches' => 0,
                'won' => 0,
                'drawn' => 0,
                'lost' => 0,
                'goal_difference' => 0,
            ]);
        }
    }
}
