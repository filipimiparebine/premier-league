"use client";

import { useState, useEffect } from "react";
import axios from "axios";
import { Button } from "@/components/ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { ScrollArea } from "@/components/ui/scroll-area";
import { useRouter } from "next/navigation";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import Image from "next/image";
import { Skeleton } from "@/components/ui/skeleton";

interface Team {
    id: number;
    name: string;
    logo: string;
}

interface Season {
    id: number;
    name: string;
}

export default function SelectTeams() {
    const [teams, setTeams] = useState<Team[]>([]);
    const [seasons, setSeasons] = useState<Season[]>([]);
    const [selectedTeams, setSelectedTeams] = useState<number[]>([]);
    const [selectedSeason, setSelectedSeason] = useState<number | null>(1);
    const [isLoading, setIsLoading] = useState<boolean>(true);
    const router = useRouter();

    useEffect(() => {
        const fetchData = async () => {
            try {
                const [teamsResponse, seasonsResponse] = await Promise.all([
                    axios.get<Team[]>("http://localhost:8000/api/teams"),
                    axios.get<Season[]>("http://localhost:8000/api/seasons"),
                ]);
                setTeams(teamsResponse.data);
                setSeasons(seasonsResponse.data);
                setIsLoading(false);
            } catch (error) {
                console.error("Error fetching data:", error);
            }
        };
        fetchData();
    }, []);

    const handleTeamToggle = (teamId: number) => {
        setSelectedTeams((prev) =>
            prev.includes(teamId)
                ? prev.filter((id) => id !== teamId)
                : [...prev, teamId]
        );
    };

    const handleSeasonToggle = (seasonId: number) => {
        setSelectedSeason((prev) => (prev === seasonId ? null : seasonId));
    };

    const handleSubmit = async () => {
        if (!selectedSeason) {
            alert("Please select a season");
            return;
        }
        if (selectedTeams.length < 2) {
            alert("Please select at least two teams");
            return;
        }
        try {
            await axios.post("http://localhost:8000/api/start-season", {
                team_ids: selectedTeams,
                season_id: selectedSeason,
            });
            router.push(`/league-table/${selectedSeason}`);
        } catch (error) {
            console.error("Error starting season:", error);
            alert("Failed to start season. Please try again.");
        }
    };

    return (
        <div className="p-4">
            <h1 className="m-auto max-w-[300px] text-2xl font-bold mb-3 text-center">
                Premier League Simulation
            </h1>
            <div className="max-w-[300px] mx-auto grid grid-cols-1 gap-8">
                {isLoading ? (
                    <div className="flex flex-col space-y-3">
                        <Skeleton className="h-[125px] w-full rounded-xl" />
                        <Skeleton className="h-[390px] w-full rounded-xl" />
                    </div>
                ) : (
                    <div className="flex flex-col space-y-3">
                        <Card className="h-[125px]">
                            <CardHeader>
                                <CardTitle>Select Season</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <Select defaultValue="1">
                                    <SelectTrigger className="w-full">
                                        <SelectValue placeholder="Season" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {seasons.map((season) => (
                                            <SelectItem
                                                key={season.id}
                                                value={season.id.toString()}
                                            >
                                                {season.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </CardContent>
                        </Card>
                        <Card className="h-[390px]">
                            <CardHeader>
                                <CardTitle>Select Teams</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <ScrollArea className="h-[300px] w-full">
                                    {teams.map((team) => (
                                        <div
                                            key={team.id}
                                            className="flex items-center space-x-2 mb-2"
                                        >
                                            <Checkbox
                                                id={`team-${team.id}`}
                                                checked={selectedTeams.includes(
                                                    team.id
                                                )}
                                                onCheckedChange={() =>
                                                    handleTeamToggle(team.id)
                                                }
                                            />
                                            <label
                                                htmlFor={`team-${team.id}`}
                                                className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                            >
                                                <Image
                                                    className="inline-block mr-2"
                                                    src={`/team-logo/${team.logo}`}
                                                    alt={team.name}
                                                    width="35"
                                                    height="35"
                                                />
                                                {team.name}
                                            </label>
                                        </div>
                                    ))}
                                </ScrollArea>
                            </CardContent>
                        </Card>
                    </div>
                )}
            </div>
            <div className="mt-3 flex justify-center">
                <Button onClick={handleSubmit}>Start Season</Button>
            </div>
        </div>
    );
}
