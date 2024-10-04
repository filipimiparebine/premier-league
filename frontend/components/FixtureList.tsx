import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import Image from "next/image";
import { FixtureListProps } from "@/types/fixture";

export function FixtureList({ fixtures, weekNumber }: FixtureListProps) {
    return (
        <Card className="max-h-[350px] overflow-hidden">
            <CardHeader>
                <CardTitle>Week {weekNumber} Fixtures</CardTitle>
            </CardHeader>
            <CardContent className="max-h-[310px] overflow-scroll">
                {fixtures.map((fixture) => (
                    <div
                        key={fixture.id}
                        className="flex items-center justify-between mb-4 text-sm"
                    >
                        <div className="flex items-center space-x-2">
                            <Image
                                src={`/team-logo/${fixture.home_team.logo}`}
                                alt={fixture.home_team.name}
                                width={24}
                                height={24}
                            />
                            <span>{fixture.home_team.name.split(" ")[0]}</span>
                        </div>
                        <div className="font-bold">
                            {fixture.home_score !== null &&
                            fixture.away_score !== null
                                ? `${fixture.home_score} - ${fixture.away_score}`
                                : "vs"}
                        </div>
                        <div className="flex items-center space-x-2">
                            <span>{fixture.away_team.name.split(" ")[0]}</span>
                            <Image
                                src={`/team-logo/${fixture.away_team.logo}`}
                                alt={fixture.away_team.name}
                                width={24}
                                height={24}
                            />
                        </div>
                    </div>
                ))}
            </CardContent>
        </Card>
    );
}
