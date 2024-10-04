<?php

namespace App\Interfaces;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

interface TeamRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Team;
    public function create(array $data): Team;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
