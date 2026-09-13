import { Form } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { useState } from 'react';
import PlantController from '@/actions/App/Http/Controllers/PlantController';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import InputError from '@/components/input-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

type SpeciesOption = {
    id: number;
    name: string;
    scientific_name: string;
};

export function AddPlantDialog({ species }: { species: SpeciesOption[] }) {
    const [open, setOpen] = useState(false);

    return (
        // modal={false} works around a known Radix issue where a Select's
        // popover, portalled to document.body, gets caught by Dialog's
        // focus-trap/pointer-lock and never opens when nested inside it.
        <Dialog open={open} onOpenChange={setOpen} modal={false}>
            <DialogTrigger asChild>
                <Button variant="outline">
                    <Plus />
                    Quick Add
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Add a plant to your garden</DialogTitle>
                </DialogHeader>

                <Form
                    {...PlantController.store.form()}
                    onSuccess={() => setOpen(false)}
                    options={{ preserveScroll: true }}
                    className="space-y-4"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="species_id">Species</Label>
                                <Select name="species_id" required>
                                    <SelectTrigger
                                        id="species_id"
                                        className="w-full"
                                    >
                                        <SelectValue placeholder="Choose a species" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {species.map((option) => (
                                            <SelectItem
                                                key={option.id}
                                                value={String(option.id)}
                                            >
                                                {option.name} —{' '}
                                                {option.scientific_name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.species_id} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="nickname">
                                    Nickname (optional)
                                </Label>
                                <Input
                                    id="nickname"
                                    name="nickname"
                                    placeholder="e.g. Balcony Neem"
                                />
                                <InputError message={errors.nickname} />
                            </div>

                            <DialogFooter>
                                <Button disabled={processing} type="submit">
                                    Add to garden
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
