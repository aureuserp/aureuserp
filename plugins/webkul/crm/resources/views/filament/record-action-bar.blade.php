@php
    $tabs = $this->getFloatingRecordTabs();
    $actions = array_filter($this->getFloatingRecordActions(), fn ($action) => $action?->isVisible());
@endphp

@if (count($tabs) > 0 || count($actions) > 0)
    <nav class="fi-tabs fi-tabs-rounded fi-tabs-segmented fi-tabs-primary crm-record-action-bar mx-auto flex">
        @foreach ($tabs as $tab)
            <a
                href="{{ $tab['url'] }}"
                @class([
                    'fi-tabs-item',
                    'fi-active' => $tab['isActive'],
                ])
            >
                @if ($tab['icon'])
                    <x-filament::icon :icon="$tab['icon']" class="fi-tabs-item-icon" />
                @endif

                <span class="fi-tabs-item-label">{{ $tab['label'] }}</span>

                @if ($tab['badge'])
                    <span class="fi-badge fi-badge-xs fi-badge-primary ml-2">{{ $tab['badge'] }}</span>
                @endif
            </a>
        @endforeach

        @if (count($tabs) > 0 && count($actions) > 0)
            <span class="crm-record-action-bar-divider" aria-hidden="true"></span>
        @endif

        @foreach ($actions as $action)
            {{ $action }}
        @endforeach
    </nav>
@endif
