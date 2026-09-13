import { Head, Link } from '@inertiajs/react';
import { Camera, Sparkles, Sprout } from 'lucide-react';
import QuizController from '@/actions/App/Http/Controllers/Onboarding/QuizController';
import { AddPlantDialog } from '@/components/add-plant-dialog';
import { GamificationSummary } from '@/components/gamification-summary';
import { PlantCard } from '@/components/plant-card';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';
import { create as diagnosisCreate } from '@/routes/diagnoses';

type PlantSummary = {
    id: number;
    nickname: string | null;
    species_name: string;
    status: string;
    needs_water: boolean;
    last_watered_at: string | null;
    photo_url: string | null;
};

type SpeciesOption = {
    id: number;
    name: string;
    scientific_name: string;
};

export default function Dashboard({
    plants,
    species,
    progress,
    attentionCount,
}: {
    plants: PlantSummary[];
    species: SpeciesOption[];
    progress: {
        xp: number;
        level: number;
        current_streak_days: number;
        co2_offset_kg: number;
    };
    attentionCount: number;
}) {
    return (
        <>
            <Head title="Dashboard" />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-xl font-semibold tracking-tight">
                            Your Garden
                        </h1>
                        {attentionCount > 0 && (
                            <p className="text-muted-foreground text-sm">
                                {attentionCount} plant
                                {attentionCount === 1 ? '' : 's'} need
                                {attentionCount === 1 ? 's' : ''} attention
                                today
                            </p>
                        )}
                    </div>
                    <div className="flex flex-wrap items-center gap-2">
                        <Button variant="outline" asChild>
                            <Link href={diagnosisCreate()}>
                                <Camera />
                                Diagnose
                            </Link>
                        </Button>
                        <Button asChild>
                            <Link href={QuizController.show()}>
                                <Sparkles />
                                Find a Plant
                            </Link>
                        </Button>
                        <AddPlantDialog species={species} />
                    </div>
                </div>

                <GamificationSummary progress={progress} />

                <div className="space-y-3">
                    <h2 className="text-sm font-semibold">Your plants</h2>

                    {plants.length === 0 ? (
                        <div className="border-sidebar-border/70 dark:border-sidebar-border flex flex-col items-center gap-3 rounded-xl border border-dashed p-10 text-center">
                            <Sprout className="text-muted-foreground size-8" />
                            <p className="text-muted-foreground text-sm">
                                Your garden is empty. Answer a few quick
                                questions and we'll recommend a plant suited to
                                your spot.
                            </p>
                            <Button asChild>
                                <Link href={QuizController.show()}>
                                    <Sparkles />
                                    Find a Plant
                                </Link>
                            </Button>
                        </div>
                    ) : (
                        <div className="grid gap-3 md:grid-cols-2">
                            {plants.map((plant) => (
                                <PlantCard key={plant.id} plant={plant} />
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
    ],
};
