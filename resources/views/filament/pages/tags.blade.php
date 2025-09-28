<x-filament-panels::page>
    <div class="grid gap-4 md:grid-cols-3">
        <form wire:submit="create">
            <div class="space-y-2">
                {{ $this->form }}
                <x-filament-panels::form.actions
                    :actions="$this->getSubmitAction()"
                    class="justify-center"
                />
            </div>
        </form>

        <div class="md:col-span-2">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
