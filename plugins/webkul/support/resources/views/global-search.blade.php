<x-filament::icon-button
    icon="heroicon-o-magnifying-glass"
    icon-size="lg"
    color="gray"
    class="fi-gs-trigger"
    :label="__('filament-panels::global-search.field.label')"
    onclick="window.__globalSearchOpen()"
/>

<style>
    .fi-topbar .fi-global-search-field { position: absolute; width: 0; height: 0; overflow: hidden; opacity: 0; pointer-events: none; }
    html.fi-gs-open .fi-gs-trigger { display: none; }
    html.fi-gs-open .fi-global-search-ctn { position: fixed; inset: 0; z-index: 60; background: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: flex-start; padding-top: 12vh; }
    html.fi-gs-open .fi-global-search { flex: 0 1 auto; width: min(42rem, 92vw); padding: 0.75rem; border-radius: 0.75rem; background: #fff; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35); }
    html.dark.fi-gs-open .fi-global-search { background: #18181b; }
    html.fi-gs-open .fi-topbar .fi-global-search-field { position: static; width: 100%; height: auto; overflow: visible; opacity: 1; pointer-events: auto; }
    html.fi-gs-open .fi-global-search-results-ctn { position: static; inset: auto; width: 100%; max-width: none; transform: none; max-height: 60vh; overflow-y: auto; margin-top: 0.5rem; box-shadow: none; }
</style>

<script>
    if (! window.__globalSearchModal) {
        window.__globalSearchModal = true;

        const root = document.documentElement;
        const field = () => document.querySelector('.fi-topbar .fi-global-search-field');

        window.__globalSearchOpen = () => {
            root.classList.add('fi-gs-open');
            setTimeout(() => field()?.querySelector('input')?.focus(), 0);
        };

        const close = () => {
            if (! root.classList.contains('fi-gs-open')) {
                return;
            }

            root.classList.remove('fi-gs-open');

            const input = field()?.querySelector('input');

            if (input) {
                input.value = '';
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.blur();
            }
        };

        document.addEventListener('focusin', (event) => {
            if (event.target.closest?.('.fi-topbar .fi-global-search-field')) {
                root.classList.add('fi-gs-open');
            }
        });

        document.addEventListener('mousedown', (event) => {
            if (event.target.classList?.contains('fi-global-search-ctn')) {
                close();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                close();
            }
        });

        document.addEventListener('livewire:navigated', close);
    }
</script>
