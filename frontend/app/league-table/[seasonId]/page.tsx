"use client";

import { useState, useEffect, useCallback } from "react";
import { useParams } from "next/navigation";
import { LeagueTable } from "@/components/LeagueTable";
import { FixtureList } from "@/components/FixtureList";
import { WeekPrediction } from "@/components/WeekPrediction";
import { Button } from "@/components/ui/button";
import axios from "axios";
import { Skeleton } from "@/components/ui/skeleton";
import { Season } from "@/types/league";
import { Fixture } from "@/types/fixture";
import { TeamPrediction } from "@/types/prediction";

export default function Leaderboard() {
    const params = useParams();
    const { seasonId } = params;
    const [seasonData, setSeasonData] = useState<Season>();
    const [fixturesData, setFixturesData] = useState<Fixture[]>([]);
    const [isLoading, setIsLoading] = useState<boolean>(true);
    const [predictionsData, setPredictionsData] = useState<TeamPrediction[]>(
        []
    );
    const [weekNumber, setWeekNumber] = useState(1);

    const fetchData = async () => {
        try {
            const [leagueTableResponse, fixturesResponse] = await Promise.all([
                axios.get<Season>(
                    `http://localhost:8000/api/league-table/${seasonId}`
                ),
                axios.get<Fixture[]>(
                    `http://localhost:8000/api/fixtures/${seasonId}/${weekNumber}`
                ),
            ]);
            setSeasonData(leagueTableResponse.data);
            setFixturesData(fixturesResponse.data);
            setIsLoading(false);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };

    useEffect(() => {
        fetchData();
    }, []);

    const getPredictions = async () => {
        try {
            const predictionsResponse = await axios.get<TeamPrediction[]>(
                `http://localhost:8000/api/week/predict/${seasonId}/${weekNumber}`
            );
            setPredictionsData(predictionsResponse.data);
        } catch (error) {
            console.error("Error fetching predictions:", error);
        }
    };

    const handleNextWeek = async () => {
        await axios
            .get<Season>(
                `http://localhost:8000/api/week/simulate/${seasonId}/${weekNumber}`
            )
            .catch((error) => console.error("Error fetching data:", error))
            .then(() => {
                fetchData();
            });

        getPredictions();
        setWeekNumber(weekNumber + 1);
    };

    return (
        <div className="container mx-auto p-4">
            <h1 className="m-auto text-2xl font-bold mb-3 text-center">
                Premier League Simulation
            </h1>
            <h2 className="m-auto text-xl mb-3 text-center">
                {isLoading ? (
                    <Skeleton className="h-[28px] w-full rounded-xl" />
                ) : (
                    `Season ${seasonData?.name}`
                )}
            </h2>

            {isLoading ? (
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-3">
                    <Skeleton className="h-[350px] lg:col-span-2 rounded-xl" />
                    <Skeleton className="h-[350px] rounded-xl" />
                </div>
            ) : (
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-3">
                    <LeagueTable stats={seasonData?.leaderboard || []} />
                    <FixtureList
                        fixtures={fixturesData}
                        weekNumber={weekNumber}
                    />
                </div>
            )}
            <div className="my-3 flex justify-between">
                <Button disabled={true}>Play all</Button>
                <Button onClick={handleNextWeek}>Next Week</Button>
            </div>
            {weekNumber > 2 && (
                <WeekPrediction
                    predictions={predictionsData}
                    weekNumber={weekNumber}
                />
            )}
        </div>
    );
}
