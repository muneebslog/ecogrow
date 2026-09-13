import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, Droplet, Leaf, Sprout, Sun } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Card } from '@/components/ui/card';
import { dashboard } from '@/routes';

type Plant = {
    id: number;
    nickname: string | null;
    location: string | null;
    status: string;
    photo_url: string | null;
    planted_at: string | null;
    last_watered_at: string | null;
    species: {
        name: string;
        scientific_name: string;
        sunlight: string;
        water_frequency_days: number;
        care_difficulty: string;
    };
};

type CareLogEntry = {
    id: number;
    action: string;
    notes: string | null;
    logged_at: string;
};

const sunlightLabel: Record<string, string> = {
    full_sun: 'Full sun',
    partial_shade: 'Partial shade',
    low_light: 'Low light',
};

const statusLabel: Record<string, string> = {
    healthy: 'Healthy',
    needs_attention: 'Needs Attention',
    critical: 'Action Required',
};

export default function PlantShow({
    plant,
    careLogs,
}: {
    plant: Plant;
    careLogs: CareLogEntry[];
}) {
    const title = plant.nickname || plant.species.name;

    return (
        <>
            <Head title={title} />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <div>
                    <Link
                        href={dashboard()}
                        className="text-muted-foreground hover:text-foreground mb-3 inline-flex items-center gap-1 text-sm"
                    >
                        <ArrowLeft className="size-4" />
                        Garden
                    </Link>

                    <div className="flex items-center justify-between gap-4">
                        <div>
                            <h1 className="text-xl font-semibold tracking-tight">
                                {title}
                            </h1>
                            <p className="text-muted-foreground text-sm italic">
                                {plant.species.scientific_name}
                            </p>
                        </div>
                        <Badge
                            variant={
                                plant.status === 'healthy'
                                    ? 'secondary'
                                    : 'default'
                            }
                        >
                            {statusLabel[plant.status] ?? plant.status}
                        </Badge>
                    </div>
                </div>

                <Card className="gap-4 p-5">
                    <h2 className="text-sm font-semibold">Care schedule</h2>
                    <div className="grid gap-4 sm:grid-cols-2">
                        <div className="flex items-center gap-3">
                            <Droplet className="text-primary size-5" />
                            <div>
                                <div className="text-sm font-medium">
                                    Watering
                                </div>
                                <div className="text-muted-foreground text-xs">
                                    Every {plant.species.water_frequency_days}{' '}
                                    days
                                    {plant.last_watered_at &&
                                        ` · Last: ${plant.last_watered_at}`}
                                </div>
                            </div>
                        </div>

                        <div className="flex items-center gap-3">
                            <Sun className="text-primary size-5" />
                            <div>
                                <div className="text-sm font-medium">
                                    Sunlight
                                </div>
                                <div className="text-muted-foreground text-xs">
                                    {sunlightLabel[plant.species.sunlight] ??
                                        plant.species.sunlight}
                                </div>
                            </div>
                        </div>

                        <div className="flex items-center gap-3">
                            <Leaf className="text-primary size-5" />
                            <div>
                                <div className="text-sm font-medium">
                                    Care difficulty
                                </div>
                                <div className="text-muted-foreground text-xs capitalize">
                                    {plant.species.care_difficulty}
                                </div>
                            </div>
                        </div>

                        <div className="flex items-center gap-3">
                            <Sprout className="text-primary size-5" />
                            <div>
                                <div className="text-sm font-medium">
                                    Planted
                                </div>
                                <div className="text-muted-foreground text-xs">
                                    {plant.planted_at ?? 'Unknown'}
                                    {plant.location && ` · ${plant.location}`}
                                </div>
                            </div>
                        </div>
                    </div>
                </Card>

                <Card className="gap-3 p-5">
                    <h2 className="text-sm font-semibold">
                        Recent care history
                    </h2>
                    {careLogs.length === 0 ? (
                        <p className="text-muted-foreground text-sm">
                            No care logged yet.
                        </p>
                    ) : (
                        <ul className="divide-border divide-y">
                            {careLogs.map((log) => (
                                <li
                                    key={log.id}
                                    className="flex items-center justify-between py-2 text-sm"
                                >
                                    <span className="capitalize">
                                        {log.action}
                                        {log.notes && (
                                            <span className="text-muted-foreground">
                                                {' '}
                                                — {log.notes}
                                            </span>
                                        )}
                                    </span>
                                    <span className="text-muted-foreground text-xs">
                                        {log.logged_at}
                                    </span>
                                </li>
                            ))}
                        </ul>
                    )}
                </Card>
            </div>
        </>
    );
}
