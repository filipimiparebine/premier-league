export interface Team {
    id: number;
    name: string;
    logo: string;
}

export interface Leaderboard {
    id: number;
    season_id: number;
    team_id: number;
    points: number;
    played_matches: number;
    won: number;
    drawn: number;
    lost: number;
    goal_difference: number;
    team: Team;
}

export interface LeagueTableProps {
    stats: Leaderboard[];
}

export interface SeasonLeaderboard {
    id: number;
    name: string;
    leaderboard: Leaderboard[];
}

export interface Season {
    id: number;
    name: string;
}
