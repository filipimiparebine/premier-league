import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import Image from "next/image";
import { FixtureListProps } from "@/types/fixture";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { useState } from "react";
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogDescription,
    DialogTitle,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Fixture } from "@/types/fixture";
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from "@/components/ui/tooltip";
import { Info } from "lucide-react";
import ApiService from "@/services/apiService";

export function FixtureList({
    fixtures,
    weekNumber,
    nextOrPlay,
    totalWeeks,
    fetchData,
}: FixtureListProps) {
    const [selectedFixture, setSelectedFixture] = useState<Fixture | null>(
        null
    );
    const [homeScore, setHomeScore] = useState<string>("");
    const [awayScore, setAwayScore] = useState<string>("");
    const handleRowClick = (fixture: Fixture) => {
        setSelectedFixture(fixture);
        setHomeScore(fixture.home_score?.toString() || "");
        setAwayScore(fixture.away_score?.toString() || "");
    };
    const handleScoreUpdate = async () => {
        try {
            const matchId = selectedFixture?.id;

            if (!matchId) {
                alert("Match ID is not defined");
                return;
            }

            const parsedHomeScore = parseInt(homeScore);
            const parsedAwayScore = parseInt(awayScore);

            if (isNaN(parsedHomeScore) || isNaN(parsedAwayScore)) {
                alert("Invalid score values");
                return;
            }
            await ApiService.updateMatchScore(
                matchId,
                parsedHomeScore,
                parsedAwayScore
            );

            setSelectedFixture(null);
            fetchData();
        } catch (error) {
            console.error("Error updating score:", error);
        }
    };
    return (
        <>
            <Card className="max-h-[350px] overflow-hidden">
                <CardHeader>
                    <div className="flex items-center space-x-2">
                        <CardTitle>
                            Week {weekNumber} / {totalWeeks}{" "}
                            {nextOrPlay ? "Fixtures" : "Results"}
                        </CardTitle>
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger asChild>
                                    <Info className="h-4 w-4 text-muted-foreground cursor-help" />
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>Click a match to edit the score</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </div>
                </CardHeader>
                <CardContent className="max-h-[310px] overflow-scroll">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-[125px]">
                                    Home
                                </TableHead>
                                <TableHead className="w-[50px] text-center">
                                    Score
                                </TableHead>
                                <TableHead className="w-[125px] text-right">
                                    Away
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody style={{ fontSize: "12px" }}>
                            {fixtures?.map((fixture) => (
                                <TableRow
                                    key={fixture.id}
                                    onClick={() => handleRowClick(fixture)}
                                    className="cursor-pointer hover:bg-muted"
                                >
                                    <TableCell>
                                        <div className="flex justify-end space-x-1">
                                            <span className="mt-[4px]">
                                                {
                                                    fixture.home_team.name.split(
                                                        " "
                                                    )[0]
                                                }
                                            </span>
                                            <Image
                                                className="inline-block"
                                                src={`/team-logo/${fixture.home_team.logo}`}
                                                alt={fixture.home_team.name}
                                                width="17"
                                                height="17"
                                                style={{
                                                    width: "auto",
                                                    height: "auto",
                                                }}
                                            />
                                        </div>
                                    </TableCell>

                                    <TableCell className="text-center">
                                        {fixture.home_score !== null &&
                                        fixture.away_score !== null
                                            ? `${fixture.home_score} - ${fixture.away_score}`
                                            : "vs"}
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex space-x-1">
                                            <Image
                                                className="inline-block"
                                                src={`/team-logo/${fixture.away_team.logo}`}
                                                alt={fixture.away_team.name}
                                                width="17"
                                                height="17"
                                                style={{
                                                    width: "auto",
                                                    height: "auto",
                                                }}
                                            />
                                            <span className="mt-[4px]">
                                                {
                                                    fixture.away_team.name.split(
                                                        " "
                                                    )[0]
                                                }
                                            </span>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
            <Dialog
                open={selectedFixture !== null}
                onOpenChange={() => setSelectedFixture(null)}
            >
                <DialogContent className="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>Edit Match Result</DialogTitle>
                        <DialogDescription>
                            Update the score for this match.
                        </DialogDescription>
                    </DialogHeader>
                    <div className="flex items-center justify-between space-x-2">
                        <div className="flex items-center space-x-2">
                            <span>
                                {selectedFixture?.home_team.name.split(" ")[0]}
                            </span>
                            <Image
                                src={
                                    selectedFixture?.home_team.logo
                                        ? `/team-logo/${selectedFixture.home_team.logo}`
                                        : "/placeholder.png"
                                }
                                alt={
                                    selectedFixture?.home_team.name ||
                                    "Home team logo"
                                }
                                width={24}
                                height={24}
                            />
                        </div>
                        <Input
                            type="number"
                            value={homeScore}
                            onChange={(e) => setHomeScore(e.target.value)}
                            className="w-16 text-center"
                            min="0"
                            aria-label="Home team score"
                        />
                        <Input
                            type="number"
                            value={awayScore}
                            onChange={(e) => setAwayScore(e.target.value)}
                            className="w-16 text-center"
                            min="0"
                            aria-label="Away team score"
                        />
                        <div className="flex items-center space-x-2">
                            <Image
                                src={
                                    selectedFixture?.away_team.logo
                                        ? `/team-logo/${selectedFixture.away_team.logo}`
                                        : "/placeholder.png"
                                }
                                alt={
                                    selectedFixture?.away_team.name ||
                                    "Away team logo"
                                }
                                width={24}
                                height={24}
                            />
                            <span>
                                {selectedFixture?.away_team.name.split(" ")[0]}
                            </span>
                        </div>
                    </div>
                    <Button onClick={handleScoreUpdate}>Update Score</Button>
                </DialogContent>
            </Dialog>
        </>
    );
}
