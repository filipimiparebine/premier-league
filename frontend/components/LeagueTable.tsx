import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import Image from "next/image";
import { LeagueTableProps } from "@/types/league";

export function LeagueTable({ stats }: LeagueTableProps) {
    return (
        <Card className="lg:col-span-2 max-h-[350px] overflow-hidden">
            <CardHeader>
                <CardTitle>League Table</CardTitle>
            </CardHeader>
            <CardContent className="h-[310px] overflow-scroll">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead className="w-[100px]">
                                Position
                            </TableHead>
                            <TableHead>Team</TableHead>
                            <TableHead>P</TableHead>
                            <TableHead>W</TableHead>
                            <TableHead>D</TableHead>
                            <TableHead>L</TableHead>
                            <TableHead>GD</TableHead>
                            <TableHead>Pts</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {stats.map((team, index) => (
                            <TableRow key={team.id}>
                                <TableCell>{index + 1}</TableCell>
                                <TableCell className="font-medium">
                                    <div className="flex items-center space-x-2">
                                        <Image
                                            className="inline-block mr-2"
                                            src={`/team-logo/${team.team.logo}`}
                                            alt={team.team.name}
                                            width="35"
                                            height="35"
                                        />
                                        <span>{team.team.name}</span>
                                    </div>
                                </TableCell>
                                <TableCell>{team.played_matches}</TableCell>
                                <TableCell>{team.won}</TableCell>
                                <TableCell>{team.drawn}</TableCell>
                                <TableCell>{team.lost}</TableCell>
                                <TableCell>{team.goal_difference}</TableCell>
                                <TableCell>{team.points}</TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    );
}
