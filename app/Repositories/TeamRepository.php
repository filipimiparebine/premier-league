<?php

namespace App\Repositories;

use App\Models\Team;
use App\Interfaces\TeamRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TeamRepository implements TeamRepositoryInterface
{
    public function all(): Collection
    {
        return Team::all();
    }

    public function find(int $id): ?Team
    {
        return Team::find($id);
    }

    public function create(array $data): Team
    {
        return Team::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $team = $this->find($id);
        if (!$team) {
            return false;
        }
        return $team->update($data);
    }

    public function delete(int $id): bool
    {
        $team = $this->find($id);
        if (!$team) {
            return false;
        }
        return $team->delete();
    }
}
