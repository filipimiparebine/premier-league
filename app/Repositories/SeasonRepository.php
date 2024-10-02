<?php

namespace App\Repositories;

use App\Models\Season;
use App\Interfaces\SeasonRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

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
}
