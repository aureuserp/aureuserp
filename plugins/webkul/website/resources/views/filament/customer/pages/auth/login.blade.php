<x-filament-panels::page class="asdasdasdad">
    <main class="w-full max-w-lg px-6 py-12 bg-white shadow-sm fi-simple-main place-self-center ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:rounded-xl sm:px-12">
        @if (filament()->hasRegistration())
            <header class="flex flex-col items-center mb-6 fi-simple-header">
                <h1 class="text-2xl font-bold tracking-tight text-center fi-simple-header-heading text-gray-950 dark:text-white">
                    {{ __('website::filament/customer/pages/auth/login.heading') }}
                </h1>

                <p class="mt-2 text-sm text-center text-gray-500 fi-simple-header-subheading dark:text-gray-400">
                    <a href="{{ filament()->getRegistrationUrl() }}" class="fi-link group/link fi-size-md fi-link-size-md fi-color-custom fi-color-primary fi-ac-action fi-ac-link-action relative inline-flex items-center justify-center gap-1.5 outline-none">
                        {{ __('website::filament/customer/pages/auth/login.actions.register.before') }}

                        <span class="text-sm font-semibold text-custom-600 group-hover/link:underline group-focus-visible/link:underline dark:text-custom-400" style="--c-400:var(--primary-400);--c-600:var(--primary-600);">
                            {{ $this->registerAction }}
                        </span>
                    </a>
                </p>
            </header>
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

        <form
            id="form"
            wire:submit="authenticate"
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

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}
    </main>
</x-filament-panels::page>
