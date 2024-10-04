<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo'
    ];


    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class);
    }

    public function homeWeeks(): HasMany
    {
        return $this->hasMany(Week::class, 'home_team_id');
    }

    public function awayWeeks(): HasMany
    {
        return $this->hasMany(Week::class, 'away_team_id');
    }
}
