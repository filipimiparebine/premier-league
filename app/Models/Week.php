<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Week extends Model
{
    use HasFactory;

    protected $fillable = [
        'season_id',
        'week_number',
        'home_team_id',
        'away_team_id',
        'home_score',
        'away_score'
    ];


    protected $with = [
        'homeTeam',
        'awayTeam'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function getTotalWeeksForSeasonAttribute(): int
    {
        return self::where('season_id', $this->season_id)->orderByDesc('week_number')->first()->week_number;
    }
}
