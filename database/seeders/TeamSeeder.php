<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $teams = [
            ['name' => 'Arsenal', 'logo' => 'arsenal.png '],
            ['name' => 'Aston Villa', 'logo' => 'aston_villa.png '],
            ['name' => 'Bournemouth', 'logo' => 'bournemouth.png '],
            ['name' => 'Brentford', 'logo' => 'brentford.png '],
            ['name' => 'Brighton & Hove Albion', 'logo' => 'brighton.png '],
            ['name' => 'Chelsea', 'logo' => 'chelsea.png '],
            ['name' => 'Crystal Palace', 'logo' => 'crystal_palace.png '],
            ['name' => 'Everton', 'logo' => 'everton.png '],
            ['name' => 'Fulham', 'logo' => 'fulham.png '],
            ['name' => 'Ipswich Town', 'logo' => 'ipswich_town.png '],
            ['name' => 'Leicester City', 'logo' => 'leicester_city.png '],
            ['name' => 'Liverpool', 'logo' => 'liverpool.png '],
            ['name' => 'Manchester City', 'logo' => 'manchester_city.png '],
            ['name' => 'Manchester United', 'logo' => 'manchester_united.png '],
            ['name' => 'Newcastle United', 'logo' => 'newcastle.png '],
            ['name' => 'Nottingham Forest', 'logo' => 'nottingham_forest.png '],
            ['name' => 'Southampton', 'logo' => 'southampton.png '],
            ['name' => 'Tottenham Hotspur', 'logo' => 'tottenham.png '],
            ['name' => 'West Ham United', 'logo' => 'west_ham.png '],
            ['name' => 'Wolverhampton Wanderers', 'logo' => 'wolves.png'],
        ];

        foreach ($teams as $team) {
            Team::create($team);
        }
    }
}
