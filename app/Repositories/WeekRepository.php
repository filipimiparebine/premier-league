<?php

namespace App\Repositories;

use App\Models\Week;
use App\Interfaces\WeekRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class WeekRepository implements WeekRepositoryInterface
{
    public function __construct(protected Week $week) {}

    public function all(): Collection
    {
        return $this->week->all();
    }

    public function find(int $id): ?Week
    {
        return $this->week->find($id);
    }

    public function create(array $data): Week
    {
        return $this->week->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $week = $this->find($id);
        if (!$week) {
            return false;
        }

        return $week->update($data);
    }

    public function delete(int $id): bool
    {
        $week = $this->find($id);
        if (!$week) {
            return false;
        }

        return $week->delete();
    }

    public function getWeek(int $seasonId, int $weekNumber): Collection
    {
        return Week::whereSeasonId($seasonId)
            ->whereWeekNumber($weekNumber)
            ->get();
    }

    public function updateMatchResult(int $matchId, int $homeScore, int $awayScore): bool
    {
        $match = $this->find($matchId);
        if (!$match) {
            return false;
        }

        return $match->update([
            'home_score' => $homeScore,
            'away_score' => $awayScore,
        ]);
    }
}
