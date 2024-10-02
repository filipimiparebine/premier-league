<?php

use App\Http\Controllers\LeagueController;
use Illuminate\Support\Facades\Route;

Route::get('/teams', [LeagueController::class, 'getTeams']);
Route::get('/seasons', [LeagueController::class, 'getSeasons']);
