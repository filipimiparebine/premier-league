<?php

namespace Database\Seeders;

use App\Models\Season;
use Illuminate\Database\Seeder;

class SeasonSeeder extends Seeder
{
    public function run(): void
    {
        for ($year = 2024; $year >= 1992; $year--) {
            $formattedYear = "{$year}/" . substr($year + 1, -2);

            $teams[] = ['name' => $formattedYear];
        }

        foreach ($teams as $team) {
            Season::updateOrCreate($team);
        }
    }
}
