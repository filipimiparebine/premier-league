<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $teams = [
            ['name' => 'Arsenal', 'logo' => 'arsenal.png'],
            ['name' => 'Aston Villa', 'logo' => 'aston_villa.png'],
            ['name' => 'Bournemouth', 'logo' => 'bournemouth.png'],
            ['name' => 'Brentford', 'logo' => 'brentford.png'],
            ['name' => 'Brighton', 'logo' => 'brighton.png'],
            ['name' => 'Burnley', 'logo' => 'burnley.png'],
            ['name' => 'Chelsea', 'logo' => 'chelsea.png'],
            ['name' => 'Crystal Palace', 'logo' => 'crystal_palace.png'],
            ['name' => 'Everton', 'logo' => 'everton.png'],
            ['name' => 'Fulham', 'logo' => 'fulham.png'],
            ['name' => 'Liverpool', 'logo' => 'liverpool.png'],
            ['name' => 'Luton', 'logo' => 'luton.png'],
            ['name' => 'Manchester City', 'logo' => 'manchester_city.png'],
            ['name' => 'Manchester United', 'logo' => 'manchester_united.png'],
            ['name' => 'Newcastle', 'logo' => 'newcastle.png'],
            ['name' => 'Nottingham Forest', 'logo' => 'nottingham_forest.png'],
            ['name' => 'Sheffield United', 'logo' => 'sheffield_united.png'],
            ['name' => 'Tottenham', 'logo' => 'tottenham.png'],
            ['name' => 'West Ham', 'logo' => 'west_ham.png'],
            ['name' => 'Wolves', 'logo' => 'wolves.png'],
        ];

        foreach ($teams as $team) {
            Team::create($team);
        }
    }
}
