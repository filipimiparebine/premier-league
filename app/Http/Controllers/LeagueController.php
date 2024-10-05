<?php

namespace App\Http\Controllers;

use App\Exceptions\WeekException;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StartSeasonRequest;
use App\Http\Requests\UpdateMatchRequest;
use App\Interfaces\LeagueServiceInterface;
use App\Interfaces\TeamRepositoryInterface;
use App\Interfaces\WeekRepositoryInterface;
use App\Interfaces\SeasonRepositoryInterface;


class LeagueController extends Controller
{
    public function __construct(
        private TeamRepositoryInterface $teamRepository,
        private SeasonRepositoryInterface $seasonRepository,
        private WeekRepositoryInterface $weekRepository,
        private LeagueServiceInterface $leagueService,
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
        $this->leagueService->generateFixtures($request->team_ids, $request->season_id);

        return response()->json(['message' => 'Season started successfully']);
    }

    public function getLeagueTable(int $seasonId): JsonResponse
    {
        $leagueTable = $this->seasonRepository->find($seasonId);

        return response()->json($leagueTable);
    }

    public function getWeekFixtures(int $seasonId, int $weekNumber): JsonResponse
    {
        $fixtures = $this->weekRepository->getWeek($seasonId, $weekNumber);

        return response()->json([
            'fixtures' => $fixtures,
            'totalWeeks' => $fixtures->first()->total_weeks_for_season
        ]);
    }

    public function simulateWeek(int $seasonId, int $weekNumber): JsonResponse
    {
        try {
            $this->leagueService->simulateWeek($seasonId, $weekNumber);
            return response()->json(['message' => 'Week simulated successfully']);
        } catch (WeekException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function updateMatchResult(UpdateMatchRequest $request, int $matchId): JsonResponse
    {
        $this->leagueService->updateMatchResult($matchId, $request->home_score, $request->away_score);

        return response()->json(['message' => 'Match result updated successfully']);
    }

    public function getMatch(int $matchId): JsonResponse
    {
        return response()->json($this->weekRepository->find($matchId));
    }

    public function predictWeek(int $seasonId, int $weekNumber): JsonResponse
    {

        try {
            return response()->json($this->leagueService->predictWeek($seasonId, $weekNumber));
        } catch (WeekException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
