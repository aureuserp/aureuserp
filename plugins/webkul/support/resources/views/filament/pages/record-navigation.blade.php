@use('Illuminate\Contracts\Support\Htmlable')
@use('Webkul\Field\Filament\Forms\Components\ProgressStepper')

<div class="fi-page-record-navigation">
    <x-filament::tabs class="fi-record-navigation-tabs">
        @foreach ($navigationItems as $navigationItem)
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
    </x-filament::tabs>

    {!! ProgressStepper::getSubNavigationSlotHtml() !!}
</div>
