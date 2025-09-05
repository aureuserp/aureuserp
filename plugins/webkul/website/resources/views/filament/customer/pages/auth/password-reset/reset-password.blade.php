<x-filament-panels::page>
    <main class="w-full max-w-lg px-6 py-12 bg-white shadow-sm fi-simple-main place-self-center ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:rounded-xl sm:px-12">
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_PASSWORD_RESET_RESET_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

        <form
            id="form"
            wire:submit="resetPassword"
            x-data="{ isProcessing: false }"
            x-on:submit="if (isProcessing) $event.preventDefault()"
            x-on:form-processing-started="isProcessing = true"
            x-on:form-processing-finished="isProcessing = false"
            class="grid fi-form gap-y-6"
        >
            <div class="flex flex-col gap-8">
                {{ $this->form }}

                <x-filament::actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()"
                />
            </div>
        </form>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_PASSWORD_RESET_RESET_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}
    </main>
</x-filament-panels::page>
