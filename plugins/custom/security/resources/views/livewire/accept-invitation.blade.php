<div>
    <x-filament-panels::page.simple>
        <x-filament-panels::form  wire:submit="create">
            {{ $this->form }}

            <x-filament-panels::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="true"
            />
        </x-filament-panels::form>
    </x-filament-panels::page.simple>
</div>
