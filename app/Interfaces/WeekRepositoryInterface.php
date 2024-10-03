<?php

namespace App\Interfaces;

use App\Models\Week;
use Illuminate\Database\Eloquent\Collection;

interface WeekRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Week;
    public function create(array $data): Week;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getWeek(int $seasonId, int $weekNumber): Collection;
}
