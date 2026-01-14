<x-filament-widgets::widget :loading="false" class="!p-0 bg-transparent shadow-none">
    @if(count($navigationItems) > 0)
        <nav
            class="fi-tabs fi-tabs-rounded fi-tabs-segmented fi-tabs-primary mx-auto flex"
            style="width: max-content;"
        >
            @foreach($navigationItems as $item)
                <a
                    href="{{ $item['url'] }}"
                    @class([
                        'fi-tabs-item',
                        'fi-active' => $item['isActive'],
                    ])
                >
                    @if($item['icon'])
                        <x-filament::icon
                            :icon="$item['icon']"
                            class="fi-tabs-item-icon"
                        />
                    @endif
                    <span class="fi-tabs-item-label">{{ $item['label'] }}</span>
                    @if($item['badge'])
                        <span class="fi-badge fi-badge-xs fi-badge-primary ml-2">
                            {{ $item['badge'] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </nav>
    @endif
</x-filament-widgets::widget>
