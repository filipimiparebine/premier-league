<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Interfaces\TeamRepositoryInterface;
use App\Interfaces\SeasonRepositoryInterface;
use App\Interfaces\WeekRepositoryInterface;
use Illuminate\Http\JsonResponse;

class LeagueController extends Controller
{
    public function __construct(
        private TeamRepositoryInterface $teamRepository,
        private SeasonRepositoryInterface $seasonRepository,
        private WeekRepositoryInterface $weekRepository
    ) {}

    public function getTeams(): JsonResponse
    {
        $teams = $this->teamRepository->all();
        return response()->json($teams);
    }
}
