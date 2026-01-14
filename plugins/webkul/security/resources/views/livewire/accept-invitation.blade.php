<div>
    <x-filament-panels::page.simple>
        <form
            id="form"
            wire:submit="create"
            x-data="{ isProcessing: false }"
            x-on:submit="if (isProcessing) $event.preventDefault()"
            x-on:form-processing-started="isProcessing = true"
            x-on:form-processing-finished="isProcessing = false"
            class="fi-form grid gap-y-6"
        >
            <div class="flex flex-col gap-8">
                {{ $this->form }}

                <x-filament::actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="true"
                />
            </div>
        </form>
    </x-filament-panels::page.simple>
</div>
