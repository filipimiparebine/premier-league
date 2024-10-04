"use client";

import { useState, useEffect } from "react";
import { useParams } from "next/navigation";
import { LeagueTable } from "@/components/LeagueTable";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import axios from "axios";
import { Skeleton } from "@/components/ui/skeleton";
import { Season } from "@/types/league";

export default function Leaderboard() {
    const params = useParams();
    const { seasonId } = params;
    const [seasonData, setSeasonData] = useState<Season>();
    const [isLoading, setIsLoading] = useState<boolean>(true);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const [leagueTableResponse] = await Promise.all([
                    axios.get<Season>(
                        `http://localhost:8000/api/league-table/${seasonId}`
                    ),
                ]);
                setSeasonData(leagueTableResponse.data);
                setIsLoading(false);
            } catch (error) {
                console.error("Error fetching data:", error);
            }
        };
        fetchData();
    }, [seasonId]);

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
                <Skeleton className="h-[400px] w-full rounded-xl" />
            ) : (
                <Card>
                    <CardHeader>
                        <CardTitle>League Table</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="overflow-x-auto">
                            <LeagueTable
                                stats={seasonData?.leaderboard || []}
                            />
                        </div>
                    </CardContent>
                </Card>
            )}
        </div>
    );
}
