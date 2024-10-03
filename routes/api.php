<?php

use App\Http\Controllers\LeagueController;
use Illuminate\Support\Facades\Route;

Route::get('/teams', [LeagueController::class, 'getTeams']);
Route::get('/seasons', [LeagueController::class, 'getSeasons']);
Route::get('/seasons/{id}', [LeagueController::class, 'getSeason']);
Route::post('/start-season', [LeagueController::class, 'startSeason']);
Route::get('/league-table/{seasonId}', [LeagueController::class, 'getLeagueTable']);
Route::get('/fixtures/{seasonId}/{weekNumber}', [LeagueController::class, 'getWeekFixtures']);
Route::get('/simulate-week/{seasonId}/{weekNumber}', [LeagueController::class, 'simulateWeek']);
