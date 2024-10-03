<?php

namespace App\Repositories;

use App\Models\Week;
use App\Interfaces\WeekRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class WeekRepository implements WeekRepositoryInterface
{
    public function all(): Collection
    {
        return Week::all();
    }

    public function find(int $id): ?Week
    {
        return Week::find($id);
    }

    public function create(array $data): Week
    {
        return Week::create($data);
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
}
