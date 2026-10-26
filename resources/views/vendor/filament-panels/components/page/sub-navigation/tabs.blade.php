@props([
    'navigation',
])

@php
    use Illuminate\Contracts\Support\Htmlable;
@endphp

<x-filament::tabs
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->class(['fi-page-sub-navigation-tabs'])"
>
    @foreach ($navigation as $navigationGroup)
        @if (! $loop->first)
            <span class="fi-page-sub-navigation-tabs-separator" aria-hidden="true"></span>
        @endif

        @foreach ($navigationGroup->getItems() as $navigationItem)
            @php
                $navigationItemBadge = $navigationItem->getBadge();
                $navigationItemIcon = $navigationItem->isActive() ? ($navigationItem->getActiveIcon() ?? $navigationItem->getIcon()) : $navigationItem->getIcon();
            @endphp

            <x-filament::tabs.item
                :active="$navigationItem->isActive()"
                :badge="$navigationItemBadge"
                :badge-color="$navigationItem->getBadgeColor($navigationItemBadge)"
                :badge-tooltip="$navigationItem->getBadgeTooltip($navigationItemBadge)"
                :href="$navigationItem->getUrl()"
                :icon="$navigationItemIcon"
                tag="a"
                :target="$navigationItem->shouldOpenUrlInNewTab() ? '_blank' : null"
                :attributes="\Filament\Support\prepare_inherited_attributes($navigationItem->getExtraAttributeBag())"
            >
                {{ $navigationItem->getLabel() }}

                @if ($navigationItemIcon instanceof Htmlable)
                    <x-slot name="icon">
                        {{ $navigationItemIcon }}
                    </x-slot>
                @endif
            </x-filament::tabs.item>
        @endforeach
    @endforeach
</x-filament::tabs>
