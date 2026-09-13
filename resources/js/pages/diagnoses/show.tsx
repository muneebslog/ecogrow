import { Head, Link } from '@inertiajs/react';
import {
    AlertTriangle,
    ArrowRight,
    CheckCircle2,
    Sparkles,
} from 'lucide-react';
import { show as plantShow } from '@/routes/plants';
import { create as diagnosisCreate } from '@/routes/diagnoses';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';

type Finding = { label: string; confidence: number; description: string };

type Diagnosis = {
    id: number;
    photo_url: string | null;
    confidence: number | null;
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

export default function DiagnosesShow({ diagnosis }: { diagnosis: Diagnosis }) {
    const isHealthy = diagnosis.health_status === 'healthy';

    return (
        <>
            <Head title="Diagnosis Result" />
            <div className="mx-auto max-w-lg p-6">
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
                </div>

                {diagnosis.findings && diagnosis.findings.length > 0 && (
                    <Card className="gap-3 p-5">
                        <h2 className="text-sm font-semibold">What we found</h2>
                        <ul className="space-y-3">
                            {diagnosis.findings.map((finding, i) => (
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
