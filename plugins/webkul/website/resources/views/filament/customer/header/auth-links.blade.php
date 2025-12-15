@if (! filament()->auth()->check())
    @php
        $visibleNavigationItems = $navigationItems->filter(fn ($item) => $item->isVisible());
        $isRtl = app()->getLocale() === 'ar';
    @endphp

    {{-- Desktop View --}}
    <ul class="items-center hidden lg:flex gap-x-4 {{ $isRtl ? 'ms-4 flex-row-reverse' : 'me-4' }}">
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
    <div class="overflow-x-auto lg:hidden" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
        <div class="flex items-center px-2 gap-x-3 {{ $isRtl ? 'flex-row-reverse' : '' }}">
            @foreach ($visibleNavigationItems as $item)
                <x-filament::link :href="$item->getUrl()" class="text-sm whitespace-nowrap">
                    {{ $item->getLabel() }}
                </x-filament::link>
            @endforeach
        </div>
    </div>
@endif
