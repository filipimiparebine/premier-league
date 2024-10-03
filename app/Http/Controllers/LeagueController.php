<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StartSeasonRequest;
use App\Services\LeagueSimulationService;
use App\Interfaces\TeamRepositoryInterface;
use App\Interfaces\WeekRepositoryInterface;
use App\Interfaces\SeasonRepositoryInterface;


class LeagueController extends Controller
{
    public function __construct(
        private TeamRepositoryInterface $teamRepository,
        private SeasonRepositoryInterface $seasonRepository,
        private WeekRepositoryInterface $weekRepository,
        private LeagueSimulationService $leagueSimulationService,
    ) {}

    public function getTeams(): JsonResponse
    {
        $teams = $this->teamRepository->all();
        return response()->json($teams);
    }

    public function getSeasons(): JsonResponse
    {
        $seasons = $this->seasonRepository->all();
        return response()->json($seasons);
    }

    public function getSeason(int $id): JsonResponse
    {
        $season = $this->seasonRepository->find($id);
        return response()->json($season);
    }

    public function startSeason(StartSeasonRequest $request): JsonResponse
    {
        $this->seasonRepository->fillLeagueTable($request->team_ids, $request->season_id);
        $this->leagueSimulationService->generateFixtures($request->team_ids, $request->season_id);

        return response()->json(['message' => 'Season started successfully']);
    }

    public function getLeagueTable(int $seasonId): JsonResponse
    {
        $leagueTable = $this->seasonRepository->find($seasonId);

        return response()->json($leagueTable);
    }
}
