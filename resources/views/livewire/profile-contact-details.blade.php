<x-filament-breezy::grid-section md=2 title="Contact Details" description="Update your phone number and address and preferred language">
    <x-filament::card>
        <form wire:submit.prevent="submit" class="space-y-6">

            {{ $this->form }}

            <div class="text-right">
                <div>
                    {{ $this->submitFormAction }} {{-- [tl! ++] --}}
                </div>
            </div>
        </form>
    </x-filament::card>
</x-filament-breezy::grid-section>
