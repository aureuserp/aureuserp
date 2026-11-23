<x-filament-panels::page>
    <div class="fi-app-menu-overlay fi-home-apps" style="top:var(--topbar-height, 3.5rem);z-index:20;overflow-y:auto;">
        <div class="fi-app-menu-backdrop"></div>

        <nav
            aria-label="{{ __('admin.app-menu.label') }}"
            class="fi-app-menu-panel"
            style="position:relative;inset:auto;width:100%;max-width:64rem;min-height:100%;margin-inline:auto;padding-top:6vh;overflow:visible;background:transparent;box-shadow:none;--tw-ring-shadow:0 0 #0000;"
        >
            <div class="fi-app-menu-grid">
                @foreach (filament()->getNavigation() as $group)
                    @php
                        $groupLabel = $group->getLabel();
                        $groupIcon = $group->getIcon();
                        $itemUrl = $group->getItems()->first()?->getUrl();
                    @endphp

                    @if (! $groupLabel || ! $itemUrl || ! $groupIcon)
                        @continue
                    @endif

                    <a
                        href="{{ $itemUrl }}"
                        @class([
                            'fi-app-menu-item',
                            'fi-active' => $group->isActive(),
                        ])
                    >
                        <x-filament::icon :icon="$groupIcon" />

                        <span>{{ $groupLabel }}</span>
                    </a>
                @endforeach
            </div>
        </nav>
    </div>

    <style>
        @media (max-width: 767px) {
            .fi-home-apps .fi-app-menu-backdrop { background: rgba(255, 255, 255, 0.9); }
            html.dark .fi-home-apps .fi-app-menu-backdrop { background: rgba(3, 7, 18, 0.9); }
        }
    </style>
</x-filament-panels::page>
