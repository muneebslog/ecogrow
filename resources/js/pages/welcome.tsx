import { Head, Link, usePage } from '@inertiajs/react';
import { ArrowRight, Leaf, Medal, ScanLine, Sprout } from 'lucide-react';
import AppLogoIcon from '@/components/app-logo-icon';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { dashboard, login, register } from '@/routes';

export default function Welcome() {
    const { auth } = usePage().props;

    return (
        <>
            <Head title="EcoGrow — Plant it. Keep it alive." />
            <div className="bg-background text-foreground min-h-screen">
                <header className="mx-auto flex max-w-6xl items-center justify-between px-6 py-6">
                    <div className="flex items-center gap-2">
                        <div className="bg-primary text-primary-foreground flex size-8 items-center justify-center rounded-md">
                            <AppLogoIcon className="size-4 fill-current" />
                        </div>
                        <span className="text-lg font-semibold tracking-tight">
                            EcoGrow
                        </span>
                    </div>

                    <nav className="flex items-center gap-3">
                        {auth.user ? (
                            <Button asChild>
                                <Link href={dashboard()}>Dashboard</Link>
                            </Button>
                        ) : (
                            <>
                                <Link
                                    href={login()}
                                    className="text-muted-foreground hover:text-foreground text-sm font-medium"
                                >
                                    Log in
                                </Link>
                                <Button asChild>
                                    <Link href={register()}>Get Started</Link>
                                </Button>
                            </>
                        )}
                    </nav>
                </header>

                <main>
                    <section className="mx-auto max-w-3xl px-6 pt-16 pb-20 text-center md:pt-24 md:pb-28">
                        <span className="bg-accent text-accent-foreground inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold">
                            <Sprout className="size-3.5" />
                            AI-powered plant care
                        </span>

                        <h1 className="mt-6 text-4xl font-semibold tracking-tight text-balance md:text-5xl">
                            Plant something. Actually keep it alive.
                        </h1>

                        <p className="text-muted-foreground mx-auto mt-5 max-w-xl text-lg text-balance">
                            EcoGrow identifies your plants, diagnoses issues
                            from a photo, and builds a care plan around your
                            exact species and climate — so good intentions turn
                            into a garden that actually grows.
                        </p>

                        <div className="mt-8 flex items-center justify-center gap-3">
                            <Button size="lg" asChild>
                                <Link href={register()}>
                                    Get Started
                                    <ArrowRight />
                                </Link>
                            </Button>
                            {!auth.user && (
                                <Button size="lg" variant="outline" asChild>
                                    <Link href={login()}>Log in</Link>
                                </Button>
                            )}
                        </div>
                    </section>

                    <section className="mx-auto max-w-5xl px-6 pb-24">
                        <div className="grid gap-6 md:grid-cols-3">
                            <Card className="gap-3 p-6">
                                <div className="bg-accent text-accent-foreground flex size-10 items-center justify-center rounded-xl">
                                    <ScanLine className="size-5" />
                                </div>
                                <h2 className="font-semibold">
                                    Instant plant diagnosis
                                </h2>
                                <p className="text-muted-foreground text-sm">
                                    Snap a photo and get a species ID, health
                                    check, and treatment plan back in seconds.
                                </p>
                            </Card>

                            <Card className="gap-3 p-6">
                                <div className="bg-accent text-accent-foreground flex size-10 items-center justify-center rounded-xl">
                                    <Leaf className="size-5" />
                                </div>
                                <h2 className="font-semibold">
                                    Personalized care plans
                                </h2>
                                <p className="text-muted-foreground text-sm">
                                    Watering, sunlight, and fertilizer schedules
                                    tuned to your exact plant and local weather.
                                </p>
                            </Card>

                            <Card className="gap-3 p-6">
                                <div className="bg-accent text-accent-foreground flex size-10 items-center justify-center rounded-xl">
                                    <Medal className="size-5" />
                                </div>
                                <h2 className="font-semibold">
                                    Track your growth
                                </h2>
                                <p className="text-muted-foreground text-sm">
                                    Earn XP, build care streaks, and see the
                                    real environmental impact of your garden.
                                </p>
                            </Card>
                        </div>
                    </section>
                </main>

                <footer className="border-border/70 border-t py-8">
                    <p className="text-muted-foreground text-center text-sm">
                        © {new Date().getFullYear()} EcoGrow
                    </p>
                </footer>
            </div>
        </>
    );
}
