<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeasonLeaderboard extends Model
{
    use HasFactory;

    protected $fillable = [
        'season_id',
        'team_id',
        'points',
        'played_matches',
        'won',
        'drawn',
        'lost',
        'goal_difference'
    ];

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function weeks(): HasMany
    {
        return $this->hasMany(Week::class);
    }
}
