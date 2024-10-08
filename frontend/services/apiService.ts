import axios from "axios";
import { Team, Season, SeasonLeaderboard } from "@/types/league";
import { FixtureResponse } from "@/types/fixture";
import { TeamPrediction } from "@/types/prediction";

const API_BASE_URL = "http://localhost:8000/api";

const apiClient = axios.create({
    baseURL: API_BASE_URL,
    headers: {
        "Content-Type": "application/json",
    },
});

export const ApiService = {
    getTeams: () => apiClient.get<Team[]>("/teams"),
    getSeasons: () => apiClient.get<Season[]>("/seasons"),
    startSeason: (teamIds: number[], seasonId: number) =>
        apiClient.post("/start-season", {
            team_ids: teamIds,
            season_id: seasonId,
        }),
    leagueTable: (seasonId: number) =>
        apiClient.get<SeasonLeaderboard>(`/league-table/${seasonId}`),
    fixtures: (seasonId: number, weekNumber: number) =>
        apiClient.get<FixtureResponse>(`/fixtures/${seasonId}/${weekNumber}`),
    predict: (seasonId: number, weekNumber: number) =>
        apiClient.get<TeamPrediction[]>(`/week/predict/${seasonId}/${weekNumber}`),
    simulate: (seasonId: number, weekNumber: number) =>
        apiClient.get<SeasonLeaderboard>(`/week/simulate/${seasonId}/${weekNumber}`),
    updateMatchScore: (matchId: number, homeScore: number, awayScore: number) =>
        apiClient.put(`/match/${matchId}`, {
            home_score: homeScore,
            away_score: awayScore,
        }),
};

export default ApiService;
