import { Form, Head } from '@inertiajs/react';
import { Camera, Sparkles, X } from 'lucide-react';
import { useRef, useState } from 'react';
import DiagnosisController from '@/actions/App/Http/Controllers/DiagnosisController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';

type PlantOption = { id: number; name: string };

export default function DiagnosesCreate({
    plants,
    preselectedPlantId,
}: {
    plants: PlantOption[];
    preselectedPlantId: number | null;
}) {
    const [preview, setPreview] = useState<string | null>(null);
    const [fileSelected, setFileSelected] = useState(false);
    const inputRef = useRef<HTMLInputElement>(null);

    function onFileChange(e: React.ChangeEvent<HTMLInputElement>) {
        const file = e.target.files?.[0];
        if (!file) {
            setPreview(null);
            setFileSelected(false);
            return;
        }
        setPreview(URL.createObjectURL(file));
        setFileSelected(true);
    }

    function clear() {
        if (inputRef.current) inputRef.current.value = '';
        setPreview(null);
        setFileSelected(false);
    }

    return (
        <>
            <Head title="Snap Diagnosis" />
            <div className="mx-auto max-w-lg p-6">
                <h1 className="text-xl font-semibold tracking-tight">
                    Snap Diagnosis
                </h1>
                <p className="text-muted-foreground mt-1 mb-6 text-sm">
                    Upload a photo of a leaf or the whole plant — we'll identify
                    the species and check for issues.
                </p>

                <Form
                    {...DiagnosisController.store.form()}
                    className="space-y-5"
                >
                    {({ processing, errors }) => (
                        <>
                            <div>
                                <input
                                    ref={inputRef}
                                    id="photo"
                                    name="photo"
                                    type="file"
                                    accept="image/png,image/jpeg,image/webp"
                                    capture="environment"
                                    onChange={onFileChange}
                                    className="hidden"
                                />

                                {preview ? (
                                    <div className="relative overflow-hidden rounded-xl border">
                                        <img
                                            src={preview}
                                            alt="Selected plant photo"
                                            className="aspect-square w-full object-cover"
                                        />
                                        <button
                                            type="button"
                                            onClick={clear}
                                            className="bg-background/90 absolute top-3 right-3 flex size-8 items-center justify-center rounded-full shadow"
                                        >
                                            <X className="size-4" />
                                        </button>
                                    </div>
                                ) : (
                                    <label
                                        htmlFor="photo"
                                        onClick={(e) => {
                                            e.preventDefault();
                                            inputRef.current?.click();
                                        }}
                                        className="border-border bg-card hover:bg-accent/40 flex aspect-square w-full cursor-pointer flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed text-center"
                                    >
                                        <Camera className="text-muted-foreground size-10" />
                                        <div>
                                            <div className="text-sm font-medium">
                                                Tap to take or upload a photo
                                            </div>
                                            <div className="text-muted-foreground mt-1 text-xs">
                                                JPEG, PNG, or WebP — up to 5MB
                                            </div>
                                        </div>
                                    </label>
                                )}
                                <InputError message={errors.photo} />
                            </div>

                            {plants.length > 0 && (
                                <div className="grid gap-2">
                                    <Label htmlFor="plant_id">
                                        Attach to an existing plant (optional)
                                    </Label>
                                    <Select
                                        name="plant_id"
                                        defaultValue={
                                            preselectedPlantId
                                                ? String(preselectedPlantId)
                                                : undefined
                                        }
                                    >
                                        <SelectTrigger
                                            id="plant_id"
                                            className="w-full"
                                        >
                                            <SelectValue placeholder="Not linked to a plant" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {plants.map((plant) => (
                                                <SelectItem
                                                    key={plant.id}
                                                    value={String(plant.id)}
                                                >
                                                    {plant.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>
                            )}

                            <div className="text-muted-foreground flex items-center justify-center gap-1.5 text-xs">
                                <Sparkles className="size-3.5" />
                                AI will identify species & check for issues
                            </div>

                            <Button
                                type="submit"
                                size="lg"
                                className="w-full"
                                disabled={!fileSelected || processing}
                            >
                                {processing && <Spinner />}
                                {processing
                                    ? 'Analyzing photo…'
                                    : 'Diagnose plant'}
                            </Button>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}
