import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import { ScrollArea } from "@/components/ui/scroll-area";

interface Team {
    id: number;
    name: string;
    logo: string;
}

interface TeamSelectorProps {
    teams: Team[];
    onTeamsSelected: (selectedTeams: number[]) => void;
}

export function TeamSelector({ teams, onTeamsSelected }: TeamSelectorProps) {
    const [selectedTeams, setSelectedTeams] = useState<number[]>([]);

    const handleTeamToggle = (teamId: number) => {
        setSelectedTeams((prev) =>
            prev.includes(teamId)
                ? prev.filter((id) => id !== teamId)
                : [...prev, teamId]
        );
    };

    const handleSubmit = () => {
        onTeamsSelected(selectedTeams);
    };

    return (
        <div className="w-full max-w-md mx-auto">
            <h2 className="text-2xl font-bold mb-4">Select Teams</h2>
            <ScrollArea className="h-[300px] w-full border rounded-md p-4">
                {teams.map((team) => (
                    <div
                        key={team.id}
                        className="flex items-center space-x-2 mb-2"
                    >
                        <Checkbox
                            id={`team-${team.id}`}
                            checked={selectedTeams.includes(team.id)}
                            onCheckedChange={() => handleTeamToggle(team.id)}
                        />
                        <label
                            htmlFor={`team-${team.id}`}
                            className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                        >
                            {team.name}
                        </label>
                    </div>
                ))}
            </ScrollArea>
            <Button onClick={handleSubmit} className="mt-4 w-full">
                Start Season
            </Button>
        </div>
    );
}
