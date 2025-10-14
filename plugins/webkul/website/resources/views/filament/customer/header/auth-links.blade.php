@if (! filament()->auth()->check())
    @php
        $visibleNavigationItems = $navigationItems->filter(fn ($item) => $item->isVisible());
    @endphp

    {{-- Desktop View --}}
    <ul class="items-center hidden lg:flex gap-x-4 me-4">
        @foreach ($visibleNavigationItems as $item)
            <li>
                <x-filament-panels::topbar.item
                    :active="$item->isActive()"
                    :active-icon="$item->getActiveIcon()"
                    :badge="$item->getBadge()"
                    :badge-color="$item->getBadgeColor()"
                    :badge-tooltip="$item->getBadgeTooltip()"
                    :icon="$item->getIcon()"
                    :should-open-url-in-new-tab="$item->shouldOpenUrlInNewTab()"
                    :url="$item->getUrl()"
                >
                    {{ $item->getLabel() }}
                </x-filament-panels::topbar.item>
            </li>
        @endforeach
    </ul>

    {{-- Mobile View --}}
    <div class="overflow-x-auto lg:hidden">
        <div class="flex items-center px-2 gap-x-3">
            @foreach ($visibleNavigationItems as $item)
                <x-filament::link :href="$item->getUrl()" class="text-sm whitespace-nowrap">
                    {{ $item->getLabel() }}
                </x-filament::link>
            @endforeach
        </div>
    </div>
@endif
