<?php

use App\Http\Controllers\LeagueController;
use Illuminate\Support\Facades\Route;

Route::get('/teams', [LeagueController::class, 'getTeams']);
Route::get('/seasons', [LeagueController::class, 'getSeasons']);
Route::get('/seasons/{id}', [LeagueController::class, 'getSeason']);
Route::post('/start-season', [LeagueController::class, 'startSeason']);
Route::get('/league-table/{seasonId}', [LeagueController::class, 'getLeagueTable']);
Route::get('/fixtures/{seasonId}/{weekNumber}', [LeagueController::class, 'getWeekFixtures']);
Route::get('/week/simulate/{seasonId}/{weekNumber}', [LeagueController::class, 'simulateWeek']);
Route::get('/week/predict/{seasonId}/{weekNumber}', [LeagueController::class, 'predictWeek']);
Route::get('/match/{matchId}', [LeagueController::class, 'getMatch']);
Route::put('/match/{matchId}', [LeagueController::class, 'updateMatchResult']);
