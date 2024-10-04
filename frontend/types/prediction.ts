import { Team } from "@/types/league";

export interface TeamPrediction {
    team: Team;
    prediction: number;
}
export interface WeekPredictionProps {
    weekNumber: number;
    predictions: TeamPrediction[];
}
