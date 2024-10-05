"use client";

import { useState, useEffect } from "react";
import { useParams, useRouter } from "next/navigation";
import { LeagueTable } from "@/components/LeagueTable";
import { FixtureList } from "@/components/FixtureList";
import { WeekPrediction } from "@/components/WeekPrediction";
import { Button } from "@/components/ui/button";
import axios from "axios";
import { Skeleton } from "@/components/ui/skeleton";
import { Season } from "@/types/league";
import { FixtureResponse } from "@/types/fixture";
import { TeamPrediction } from "@/types/prediction";
import { Progress } from "@/components/ui/progress";
import { Home } from "lucide-react";

export default function Leaderboard() {
    const params = useParams();
    const { seasonId } = params;
    const [seasonData, setSeasonData] = useState<Season>();
    const [fixturesData, setFixturesData] = useState<FixtureResponse>();
    const [isLoading, setIsLoading] = useState<boolean>(true);
    const [predictionsData, setPredictionsData] = useState<TeamPrediction[]>(
        []
    );
    const [weekNumber, setWeekNumber] = useState(1);
    const [nextOrPlay, setNextOrPlay] = useState(true);
    const [totalWeeks, setTotalWeeks] = useState(0);
    const [progress, setProgress] = useState(0);
    const [playAllInProgress, setPlayAllInProgress] = useState(false);
    const router = useRouter();

    const navigateHome = () => {
        router.push("/");
    };

    const fetchData = async () => {
        try {
            const [leagueTableResponse, fixturesResponse, predictionsResponse] =
                await Promise.all([
                    axios.get<Season>(
                        `http://localhost:8000/api/league-table/${seasonId}`
                    ),
                    axios.get<FixtureResponse>(
                        `http://localhost:8000/api/fixtures/${seasonId}/${weekNumber}`
                    ),
                    axios.get<TeamPrediction[]>(
                        `http://localhost:8000/api/week/predict/${seasonId}/${weekNumber}`
                    ),
                ]);
            setSeasonData(leagueTableResponse.data);
            setFixturesData(fixturesResponse.data);
            setPredictionsData(predictionsResponse.data);
            if (!totalWeeks) {
                setTotalWeeks(fixturesResponse.data.totalWeeks);
            }
            setIsLoading(false);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };

    useEffect(() => {
        fetchData();
    }, [weekNumber]);

    const handleNextWeek = async () => {
        try {
            setNextOrPlay(true);
            fetchData();
            setWeekNumber((prevWeek) => prevWeek + 1);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };

    const handlePlayWeek = async (weekNo: number) => {
        try {
            await axios.get<Season>(
                `http://localhost:8000/api/week/simulate/${seasonId}/${weekNo}`
            );
            setNextOrPlay(false);
            setProgress((weekNo / totalWeeks) * 100);
            fetchData();
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };
    const delay = (ms: number) =>
        new Promise((resolve) => setTimeout(resolve, ms));

    const handlePlayAll = async () => {
        setPlayAllInProgress(true);
        for (let i = weekNumber; i <= totalWeeks; i++) {
            await handlePlayWeek(i);
            await delay(500);
            if (i !== totalWeeks) {
                await handleNextWeek();
                await delay(500);
            }
            setProgress((i / totalWeeks) * 100);
        }
        setPlayAllInProgress(false);
    };

    return (
        <div className="container mx-auto p-4">
            <Home className="m-auto cursor-pointer" onClick={navigateHome} />
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
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-3 ">
                    <LeagueTable stats={seasonData?.leaderboard || []} />
                    <FixtureList
                        fixtures={fixturesData?.fixtures}
                        weekNumber={weekNumber}
                        nextOrPlay={nextOrPlay}
                        totalWeeks={totalWeeks}
                        fetchData={fetchData}
                    />
                </div>
            )}
            <div className="my-3 flex justify-between">
                <Button
                    onClick={handlePlayAll}
                    disabled={playAllInProgress || weekNumber >= totalWeeks}
                >
                    Play all
                </Button>

                <Progress value={progress} className="w-[200px] mt-3" />
                {nextOrPlay ? (
                    <Button onClick={() => handlePlayWeek(weekNumber)}>
                        Play Week
                    </Button>
                ) : (
                    <Button
                        onClick={() => handleNextWeek()}
                        disabled={weekNumber >= totalWeeks}
                    >
                        Next Week
                    </Button>
                )}
            </div>

            {isLoading ? (
                <Skeleton className="h-[115px] rounded-xl" />
            ) : (
                <WeekPrediction
                    predictions={predictionsData}
                    weekNumber={weekNumber}
                />
            )}
        </div>
    );
}
