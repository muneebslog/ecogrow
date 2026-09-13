import { Flame, Leaf, Medal } from 'lucide-react';
import { Card } from '@/components/ui/card';

type Progress = {
    xp: number;
    level: number;
    current_streak_days: number;
    co2_offset_kg: number;
};

export function GamificationSummary({ progress }: { progress: Progress }) {
    return (
        <Card className="flex-row flex-wrap items-center justify-between gap-4 p-4">
            <div className="flex items-center gap-2">
                <Medal className="text-primary size-5" />
                <div>
                    <div className="text-sm font-semibold">
                        Level {progress.level}
                    </div>
                    <div className="text-muted-foreground text-xs">
                        {progress.xp} XP
                    </div>
                </div>
            </div>

            <div className="flex items-center gap-2">
                <Flame className="size-5 text-orange-500" />
                <div>
                    <div className="text-sm font-semibold">
                        {progress.current_streak_days}-day streak
                    </div>
                    <div className="text-muted-foreground text-xs">
                        Care streak
                    </div>
                </div>
            </div>

            <div className="flex items-center gap-2">
                <Leaf className="text-primary size-5" />
                <div>
                    <div className="text-sm font-semibold">
                        {progress.co2_offset_kg.toFixed(1)} kg
                    </div>
                    <div className="text-muted-foreground text-xs">
                        CO2 offset
                    </div>
                </div>
            </div>
        </Card>
    );
}
