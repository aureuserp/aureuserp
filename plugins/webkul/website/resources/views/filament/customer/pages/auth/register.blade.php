<x-filament-panels::page class="">
    <main class="w-full max-w-lg px-6 py-12 bg-white shadow-sm fi-simple-main place-self-center ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:rounded-xl sm:px-12">
        @if (filament()->hasLogin())
            <header class="flex flex-col items-center mb-6 fi-simple-header">
                <h1 class="text-2xl font-bold tracking-tight text-center fi-simple-header-heading text-gray-950 dark:text-white">
                    {{ __('filament-panels::pages/auth/register.heading') }}
                </h1>

                <p class="mt-2 text-sm text-center text-gray-500 fi-simple-header-subheading dark:text-gray-400">
                    <a href="http://127.0.0.1:8000/login" class="fi-link group/link fi-size-md fi-link-size-md fi-color-custom fi-color-primary fi-ac-action fi-ac-link-action relative inline-flex items-center justify-center gap-1.5 outline-none">
                        {{ __('filament-panels::pages/auth/register.actions.login.before') }}

                        <span class="text-sm font-semibold text-custom-600 group-hover/link:underline group-focus-visible/link:underline dark:text-custom-400" style="--c-400:var(--primary-400);--c-600:var(--primary-600);">
                            {{ $this->loginAction }}
                        </span>
                    </a>
                </p>
            </header>
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_REGISTER_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

        <x-filament-panels::form id="form" wire:submit="register">
            {{ $this->form }}

            <x-filament-panels::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="$this->hasFullWidthFormActions()"
            />
        </x-filament-panels::form>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_REGISTER_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}
    </main>
</x-filament-panels::page>
