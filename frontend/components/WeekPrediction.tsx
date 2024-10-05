import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import Image from "next/image";
import { WeekPredictionProps } from "@/types/prediction";

export function WeekPrediction({
    weekNumber,
    predictions,
}: WeekPredictionProps) {
    return (
        <Card className="min-h-[115px] lg:col-span-3">
            <CardHeader>
                <CardTitle>Week {weekNumber + 1} Predictions</CardTitle>
            </CardHeader>
            <CardContent>
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                    {predictions.length > 0 ? (
                        predictions.map((prediction) => (
                            <div
                                key={prediction.team.id}
                                className="flex items-center space-x-2 text-sm"
                            >
                                <Image
                                    src={`/team-logo/${prediction.team.logo}`}
                                    alt={prediction.team.name}
                                    width={24}
                                    height={24}
                                />
                                <span>
                                    {prediction.team.name.split(" ")[0]}
                                </span>
                                <span className="font-bold">
                                    {prediction.prediction}%
                                </span>
                            </div>
                        ))
                    ) : (
                        <div>No predictions available.</div>
                    )}
                </div>
            </CardContent>
        </Card>
    );
}
