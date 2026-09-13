import { Head, Link } from '@inertiajs/react';
import {
    AlertTriangle,
    ArrowRight,
    CheckCircle2,
    Info,
    Sparkles,
    X,
} from 'lucide-react';
import { show as plantShow } from '@/routes/plants';
import { create as diagnosisCreate } from '@/routes/diagnoses';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { dashboard } from '@/routes';

type Finding = { label: string; confidence: number; description: string };

type Diagnosis = {
    id: number;
    photo_url: string | null;
    species_name: string | null;
    scientific_name: string | null;
    confidence: number | null;
    is_plant_confidence: number | null;
    health_status: string;
    findings: Finding[] | null;
    recommendation: string | null;
    plant: { id: number; name: string } | null;
};

const healthLabel: Record<string, string> = {
    healthy: 'Healthy',
    stressed: 'Under Stress',
    diseased: 'Disease Detected',
    pest: 'Pest Infestation',
    unknown: 'Inconclusive',
};

const LOW_CONFIDENCE_THRESHOLD = 40;

export default function DiagnosesShow({ diagnosis }: { diagnosis: Diagnosis }) {
    const isHealthy = diagnosis.health_status === 'healthy';
    const isUnknownHealth = diagnosis.health_status === 'unknown';
    const isLowConfidence =
        (diagnosis.confidence ?? 100) < LOW_CONFIDENCE_THRESHOLD ||
        (diagnosis.is_plant_confidence ?? 100) < LOW_CONFIDENCE_THRESHOLD;
    const hasFindings = !!diagnosis.findings && diagnosis.findings.length > 0;

    return (
        <>
            <Head title="Diagnosis Result" />
            <div className="mx-auto max-w-lg p-6">
                <div className="mb-4 flex items-center justify-end">
                    <Link
                        href={dashboard()}
                        className="text-muted-foreground hover:text-foreground flex size-8 items-center justify-center rounded-full"
                    >
                        <X className="size-5" />
                    </Link>
                </div>

                {diagnosis.photo_url && (
                    <img
                        src={diagnosis.photo_url}
                        alt="Diagnosed plant"
                        className="mb-6 aspect-square w-full rounded-xl border object-cover"
                    />
                )}

                {diagnosis.confidence !== null && (
                    <Badge variant="secondary" className="mb-3">
                        <Sparkles className="size-3" />
                        Identified · {diagnosis.confidence}% confidence
                    </Badge>
                )}

                {diagnosis.species_name && (
                    <div className="mb-4">
                        <h1 className="text-xl font-semibold tracking-tight">
                            {diagnosis.species_name}
                        </h1>
                        {diagnosis.scientific_name &&
                            diagnosis.scientific_name !==
                                diagnosis.species_name && (
                                <p className="text-muted-foreground text-sm italic">
                                    {diagnosis.scientific_name}
                                </p>
                            )}
                    </div>
                )}

                {isLowConfidence && (
                    <Card className="mb-4 flex-row items-start gap-2 border-none bg-amber-500/10 p-3">
                        <Info className="mt-0.5 size-4 shrink-0 text-amber-600 dark:text-amber-400" />
                        <p className="text-xs text-amber-700 dark:text-amber-400">
                            Low confidence on this photo — for a more reliable
                            result, try a clear, well-lit close-up of the
                            leaves.
                        </p>
                    </Card>
                )}

                <div className="mb-4">
                    <Badge variant={isHealthy ? 'secondary' : 'destructive'}>
                        {isHealthy ? (
                            <CheckCircle2 className="size-3" />
                        ) : (
                            <AlertTriangle className="size-3" />
                        )}
                        {healthLabel[diagnosis.health_status] ??
                            diagnosis.health_status}
                    </Badge>
                    {isUnknownHealth && (
                        <p className="text-muted-foreground mt-2 text-xs">
                            We couldn't confidently assess this plant's health
                            from the photo provided.
                        </p>
                    )}
                </div>

                {hasFindings && (
                    <Card className="gap-3 p-5">
                        <h2 className="text-sm font-semibold">What we found</h2>
                        <ul className="space-y-3">
                            {diagnosis.findings!.map((finding, i) => (
                                <li key={i} className="text-sm">
                                    <div className="font-medium">
                                        {finding.label}
                                    </div>
                                    {finding.description && (
                                        <div className="text-muted-foreground text-xs">
                                            {finding.description}
                                        </div>
                                    )}
                                </li>
                            ))}
                        </ul>
                    </Card>
                )}

                {diagnosis.recommendation && (
                    <Card className="bg-accent mt-4 gap-2 border-none p-5">
                        <h2 className="text-accent-foreground text-sm font-semibold">
                            Recommended action
                        </h2>
                        <p className="text-accent-foreground text-sm">
                            {diagnosis.recommendation}
                        </p>
                    </Card>
                )}

                <div className="mt-8">
                    {diagnosis.plant ? (
                        <Button asChild size="lg" className="w-full">
                            <Link href={plantShow(diagnosis.plant.id)}>
                                View {diagnosis.plant.name}'s care plan
                                <ArrowRight />
                            </Link>
                        </Button>
                    ) : (
                        <Button asChild size="lg" className="w-full">
                            <Link href={diagnosisCreate()}>
                                Diagnose another plant
                            </Link>
                        </Button>
                    )}
                </div>
            </div>
        </>
    );
}
