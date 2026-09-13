import { Form, Head } from '@inertiajs/react';
import { CheckCircle2, Leaf, MapPin } from 'lucide-react';
import { useState } from 'react';
import PlantController from '@/actions/App/Http/Controllers/PlantController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { cn } from '@/lib/utils';

type SpeciesMatch = {
    species_id: number;
    name: string;
    scientific_name: string;
    category: string;
    care_difficulty: string;
    native_region: string | null;
    match_percent: number;
};

const sunlightLabel: Record<string, string> = {
    full_sun: 'full sun',
    partial_shade: 'partial shade',
    low_light: 'low light',
};

const placementLabel: Record<string, string> = {
    balcony: 'balcony pot',
    yard: 'yard planting',
    indoor: 'indoor placement',
};

export default function Recommendations({
    sunlight,
    placement,
    matches,
}: {
    sunlight: string;
    placement: string;
    matches: SpeciesMatch[];
}) {
    const [selectedId, setSelectedId] = useState(matches[0]?.species_id);
    const selected = matches.find((m) => m.species_id === selectedId);

    return (
        <>
            <Head title="Recommended for you" />
            <div className="mx-auto max-w-lg p-6">
                <h1 className="text-2xl font-semibold tracking-tight">
                    Recommended for you
                </h1>
                <div className="text-muted-foreground mt-1 mb-6 flex items-center gap-1.5 text-sm">
                    <MapPin className="size-3.5" />
                    {sunlightLabel[sunlight] ?? sunlight} ·{' '}
                    {placementLabel[placement] ?? placement}
                </div>

                <div className="flex flex-col gap-3">
                    {matches.map((match) => {
                        const isSelected = match.species_id === selectedId;

                        return (
                            <button
                                key={match.species_id}
                                type="button"
                                onClick={() => setSelectedId(match.species_id)}
                                className={cn(
                                    'flex items-start gap-3 rounded-xl border p-4 text-left transition-colors',
                                    isSelected
                                        ? 'border-primary bg-accent'
                                        : 'border-border bg-card hover:bg-accent/40',
                                )}
                            >
                                <div className="bg-primary/10 text-primary flex size-12 shrink-0 items-center justify-center rounded-xl">
                                    <Leaf className="size-6" />
                                </div>

                                <div className="min-w-0 flex-1">
                                    <div className="flex items-center justify-between gap-2">
                                        <span className="font-semibold">
                                            {match.name}
                                        </span>
                                        {isSelected && (
                                            <CheckCircle2 className="text-primary size-5 shrink-0" />
                                        )}
                                    </div>
                                    <div className="text-muted-foreground text-xs italic">
                                        {match.scientific_name}
                                    </div>
                                    <div className="mt-2 flex flex-wrap gap-1.5">
                                        <Badge variant="secondary">
                                            {match.match_percent}% match
                                        </Badge>
                                        <Badge variant="outline">
                                            {match.care_difficulty} care
                                        </Badge>
                                    </div>
                                </div>
                            </button>
                        );
                    })}
                </div>

                <Form {...PlantController.store.form()} className="mt-8">
                    {({ processing }) => (
                        <>
                            <input
                                type="hidden"
                                name="species_id"
                                value={selectedId ?? ''}
                            />
                            <Button
                                type="submit"
                                size="lg"
                                className="w-full"
                                disabled={!selected || processing}
                            >
                                {processing && <Spinner />}
                                Add {selected?.name ?? ''} to my garden
                            </Button>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}
