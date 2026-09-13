import { Head, Link, useForm } from '@inertiajs/react';
import { Home, Sprout, Sun, TreePine, X } from 'lucide-react';
import { useState } from 'react';
import QuizController from '@/actions/App/Http/Controllers/Onboarding/QuizController';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { cn } from '@/lib/utils';
import { dashboard } from '@/routes';

const sunlightOptions = [
    {
        value: 'full_sun',
        label: 'Full sun',
        description: '6+ hours of direct light',
        icon: Sun,
    },
    {
        value: 'partial_shade',
        label: 'Partial shade',
        description: '2–6 hours of light',
        icon: Sun,
    },
    {
        value: 'low_light',
        label: 'Low light',
        description: 'Mostly indoors',
        icon: Sun,
    },
] as const;

const placementOptions = [
    { value: 'balcony', label: 'Balcony pot', icon: Home },
    { value: 'yard', label: 'Ground / yard', icon: TreePine },
    { value: 'indoor', label: 'Indoor', icon: Home },
] as const;

export default function Quiz() {
    const [step, setStep] = useState(1);
    const { data, setData, post, processing } = useForm({
        sunlight: '',
        placement: '',
    });

    const canContinue = step === 1 ? !!data.sunlight : !!data.placement;

    function next() {
        if (step === 1) {
            setStep(2);
            return;
        }

        post(QuizController.submit.url());
    }

    return (
        <>
            <Head title="Find a Plant" />
            <div className="mx-auto flex min-h-screen max-w-lg flex-col p-6">
                <div className="mb-6 flex items-center justify-end">
                    <Link
                        href={dashboard()}
                        className="text-muted-foreground hover:text-foreground flex size-8 items-center justify-center rounded-full"
                    >
                        <X className="size-5" />
                    </Link>
                </div>

                <div className="mb-8 flex gap-2">
                    <span
                        className={cn(
                            'h-1.5 flex-1 rounded-full',
                            step >= 1 ? 'bg-primary' : 'bg-border',
                        )}
                    />
                    <span
                        className={cn(
                            'h-1.5 flex-1 rounded-full',
                            step >= 2 ? 'bg-primary' : 'bg-border',
                        )}
                    />
                </div>

                <span className="text-primary mb-2 text-xs font-bold tracking-wide uppercase">
                    Step {step} of 2
                </span>

                {step === 1 ? (
                    <>
                        <h1 className="mb-2 text-2xl font-semibold tracking-tight">
                            Let's find your perfect plant
                        </h1>
                        <p className="text-muted-foreground mb-8 text-sm">
                            A few quick questions so we can recommend species
                            that will actually thrive in your spot.
                        </p>

                        <h2 className="mb-3 text-sm font-semibold">
                            How much sunlight does this spot get?
                        </h2>
                        <div className="flex flex-col gap-2">
                            {sunlightOptions.map((option) => (
                                <button
                                    key={option.value}
                                    type="button"
                                    onClick={() =>
                                        setData('sunlight', option.value)
                                    }
                                    className={cn(
                                        'flex items-center gap-3 rounded-xl border p-4 text-left transition-colors',
                                        data.sunlight === option.value
                                            ? 'border-primary bg-accent'
                                            : 'border-border bg-card hover:bg-accent/40',
                                    )}
                                >
                                    <option.icon className="text-muted-foreground size-5 shrink-0" />
                                    <div>
                                        <div className="text-sm font-medium">
                                            {option.label}
                                        </div>
                                        <div className="text-muted-foreground text-xs">
                                            {option.description}
                                        </div>
                                    </div>
                                </button>
                            ))}
                        </div>
                    </>
                ) : (
                    <>
                        <h1 className="mb-2 text-2xl font-semibold tracking-tight">
                            Where will you plant it?
                        </h1>
                        <p className="text-muted-foreground mb-8 text-sm">
                            This helps us recommend species suited to the space.
                        </p>

                        <div className="grid grid-cols-3 gap-2">
                            {placementOptions.map((option) => (
                                <button
                                    key={option.value}
                                    type="button"
                                    onClick={() =>
                                        setData('placement', option.value)
                                    }
                                    className={cn(
                                        'flex flex-col items-center gap-2 rounded-xl border p-4 text-center transition-colors',
                                        data.placement === option.value
                                            ? 'border-primary bg-accent'
                                            : 'border-border bg-card hover:bg-accent/40',
                                    )}
                                >
                                    <option.icon className="text-muted-foreground size-6" />
                                    <span className="text-xs font-medium">
                                        {option.label}
                                    </span>
                                </button>
                            ))}
                        </div>
                    </>
                )}

                <div className="mt-auto pt-10">
                    <Button
                        className="w-full"
                        size="lg"
                        disabled={!canContinue || processing}
                        onClick={next}
                    >
                        {processing && <Spinner />}
                        {step === 1 ? 'Continue' : 'See recommended species'}
                    </Button>
                    {step === 2 && (
                        <div className="mt-2 flex items-center justify-center gap-1.5 text-center">
                            <Sprout className="text-muted-foreground size-3.5" />
                            <span className="text-muted-foreground text-xs">
                                We'll match species suited to your area
                            </span>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
