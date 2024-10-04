<?php

namespace App\Interfaces;

use App\Models\Season;
use Illuminate\Database\Eloquent\Collection;

interface SeasonRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Season;
    public function create(array $data): Season;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function updateTeamStats(int $seasonId, int $teamId, array $stats): bool;
    public function fillLeagueTable(array $teamIds, int $seasonId): void;
}
