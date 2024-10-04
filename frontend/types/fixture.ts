import { Team } from "@/types/league";

export interface Fixture {
    id: number;
    season_id: number;
    week_number: number;
    home_team_id: number;
    away_team_id: number;
    home_score: number | null;
    away_score: number | null;
    home_team: Team;
    away_team: Team;
}
export interface FixtureListProps {
    fixtures: Fixture[];
    weekNumber: number;
}
