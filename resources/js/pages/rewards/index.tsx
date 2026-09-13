import { Head } from '@inertiajs/react';
import { Award, Flame, Lock, Sprout, TreePine } from 'lucide-react';
import { Card } from '@/components/ui/card';
import { cn } from '@/lib/utils';

type Progress = {
    level: number;
    level_name: string;
    xp: number;
    xp_floor: number;
    xp_ceiling: number | null;
    current_streak_days: number;
    longest_streak_days: number;
    co2_offset_kg: number;
};

type BadgeItem = {
    id: number;
    code: string;
    name: string;
    description: string;
    icon: string;
    earned: boolean;
};

export default function RewardsIndex({
    progress,
    plantCount,
    badges,
}: {
    progress: Progress;
    plantCount: number;
    badges: BadgeItem[];
}) {
    const xpIntoLevel = progress.xp - progress.xp_floor;
    const xpForLevel = progress.xp_ceiling
        ? progress.xp_ceiling - progress.xp_floor
        : null;
    const percent = xpForLevel
        ? Math.min(100, Math.round((xpIntoLevel / xpForLevel) * 100))
        : 100;

    return (
        <>
            <Head title="Rewards & Impact" />
            <div className="mx-auto max-w-lg space-y-4 p-4">
                <div>
                    <h1 className="text-xl font-semibold tracking-tight">
                        Your Growth
                    </h1>
                    <p className="text-muted-foreground text-sm">
                        Level {progress.level} · {progress.level_name}
                    </p>
                </div>

                <Card className="bg-accent gap-3 border-none p-5">
                    <div className="flex items-center justify-between">
                        <div className="text-accent-foreground flex items-center gap-2 text-sm font-semibold">
                            <Award className="size-5" />
                            Level {progress.level}
                        </div>
                        <span className="text-accent-foreground text-xs font-semibold">
                            {progress.xp_ceiling
                                ? `${progress.xp} / ${progress.xp_ceiling} XP`
                                : `${progress.xp} XP · Max level`}
                        </span>
                    </div>
                    <div className="bg-background/60 h-2 overflow-hidden rounded-full">
                        <div
                            className="bg-primary h-full rounded-full"
                            style={{ width: `${percent}%` }}
                        />
                    </div>
                </Card>

                <Card className="flex-row items-center gap-4 p-5">
                    <div className="bg-accent text-accent-foreground flex size-11 shrink-0 items-center justify-center rounded-xl">
                        <Flame className="size-5" />
                    </div>
                    <div className="flex-1">
                        <div className="font-semibold">
                            {progress.current_streak_days}-day care streak
                        </div>
                        <div className="text-muted-foreground text-xs">
                            Longest: {progress.longest_streak_days} days
                        </div>
                    </div>
                </Card>

                <div className="grid grid-cols-3 gap-2">
                    <Card className="items-center gap-1 p-4 text-center">
                        <TreePine className="text-primary size-5" />
                        <div className="text-sm font-bold">
                            {progress.co2_offset_kg.toFixed(1)} kg
                        </div>
                        <div className="text-muted-foreground text-[11px]">
                            CO2 offset
                        </div>
                    </Card>
                    <Card className="items-center gap-1 p-4 text-center">
                        <Sprout className="text-primary size-5" />
                        <div className="text-sm font-bold">{plantCount}</div>
                        <div className="text-muted-foreground text-[11px]">
                            Plants growing
                        </div>
                    </Card>
                    <Card className="items-center gap-1 p-4 text-center">
                        <Award className="text-primary size-5" />
                        <div className="text-sm font-bold">
                            {badges.filter((b) => b.earned).length}
                        </div>
                        <div className="text-muted-foreground text-[11px]">
                            Badges earned
                        </div>
                    </Card>
                </div>

                <div>
                    <h2 className="mb-2 text-sm font-semibold">Badges</h2>
                    <div className="grid grid-cols-4 gap-3">
                        {badges.map((badge) => (
                            <div
                                key={badge.id}
                                className="flex flex-col items-center gap-1.5 text-center"
                                title={badge.description}
                            >
                                <div
                                    className={cn(
                                        'relative flex size-14 items-center justify-center rounded-full',
                                        badge.earned
                                            ? 'bg-accent text-accent-foreground'
                                            : 'bg-muted text-muted-foreground',
                                    )}
                                >
                                    <Award className="size-6" />
                                    {!badge.earned && (
                                        <div className="bg-background absolute -right-0.5 -bottom-0.5 flex size-5 items-center justify-center rounded-full border">
                                            <Lock className="text-muted-foreground size-2.5" />
                                        </div>
                                    )}
                                </div>
                                <span
                                    className={cn(
                                        'text-[11px] leading-tight font-medium',
                                        !badge.earned &&
                                            'text-muted-foreground',
                                    )}
                                >
                                    {badge.name}
                                </span>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </>
    );
}
