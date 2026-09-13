import { Link } from '@inertiajs/react';
import { Droplet, Leaf } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Card } from '@/components/ui/card';
import { show } from '@/routes/plants';

type PlantSummary = {
    id: number;
    nickname: string | null;
    species_name: string;
    status: string;
    needs_water: boolean;
    last_watered_at: string | null;
    photo_url: string | null;
};

const statusVariant: Record<string, 'default' | 'secondary' | 'destructive'> = {
    healthy: 'secondary',
    needs_attention: 'default',
    critical: 'destructive',
};

const statusLabel: Record<string, string> = {
    healthy: 'Healthy',
    needs_attention: 'Needs Attention',
    critical: 'Action Required',
};

export function PlantCard({ plant }: { plant: PlantSummary }) {
    const label = plant.needs_water
        ? 'Needs Water'
        : (statusLabel[plant.status] ?? plant.status);
    const variant = plant.needs_water
        ? 'default'
        : (statusVariant[plant.status] ?? 'secondary');

    return (
        <Link href={show(plant.id)} className="block">
            <Card className="hover:bg-accent/40 flex-row items-center gap-4 p-4 transition-colors">
                <div className="bg-primary/10 text-primary flex size-12 shrink-0 items-center justify-center rounded-xl">
                    <Leaf className="size-6" />
                </div>

                <div className="min-w-0 flex-1">
                    <div className="truncate font-medium">
                        {plant.nickname || plant.species_name}
                    </div>
                    <div className="text-muted-foreground flex items-center gap-1 truncate text-sm">
                        {plant.needs_water && <Droplet className="size-3.5" />}
                        {plant.last_watered_at
                            ? `Watered ${plant.last_watered_at}`
                            : 'Not watered yet'}
                    </div>
                </div>

                <Badge variant={variant}>{label}</Badge>
            </Card>
        </Link>
    );
}
