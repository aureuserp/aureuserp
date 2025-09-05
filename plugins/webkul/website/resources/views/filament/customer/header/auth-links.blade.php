@if (! filament()->auth()->check())
    @php
        $visibleNavigationItems = $navigationItems->filter(fn ($item) => $item->isVisible());
    @endphp

    {{-- Desktop View --}}
    <ul class="items-center hidden me-4 gap-x-4 lg:flex">
        @foreach ($visibleNavigationItems as $item)
            <li class="transition-all duration-200 transform hover:scale-105">
                <x-filament-panels::topbar.item
                    :active="$item->isActive()"
                    :active-icon="$item->getActiveIcon()"
                    :badge="$item->getBadge()"
                    :badge-color="$item->getBadgeColor()"
                    :badge-tooltip="$item->getBadgeTooltip()"
                    :icon="$item->getIcon()"
                    :should-open-url-in-new-tab="$item->shouldOpenUrlInNewTab()"
                    :url="$item->getUrl()"
                    class="relative overflow-hidden group"
                >
                    <span class="relative z-10 transition-colors duration-200">
                        {{ $item->getLabel() }}
                    </span>
                </x-filament-panels::topbar.item>
            </li>
        @endforeach
    </ul>

    {{-- Mobile View --}}
    <div class="lg:hidden">
        <div class="flex items-center gap-x-3">
            @foreach ($visibleNavigationItems as $item)
                <x-filament::link :href="$item->getUrl()" tag="a">
                    <span class="text-gray-700 dark:text-gray-200 hover:no-underline">
                        {{ $item->getLabel() }}
                    </span>
                </x-filament::link>
            @endforeach
        </div>
    </div>
@endif
