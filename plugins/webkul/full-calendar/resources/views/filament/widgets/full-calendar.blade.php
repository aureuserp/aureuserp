@php
    $plugin = \Webkul\FullCalendar\FullCalendarPlugin::get();
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex justify-end flex-1 mb-4">
            <x-filament::actions
                :actions="$this->getCachedHeaderActions()"
                class="shrink-0"
            />
        </div>

        <div
            class="full-calendar"
            wire:ignore
            x-load
            x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('full-calendar', 'full-calendar') }}"
            x-data="fullCalendar({
                locale: @js($plugin->getLocale()),
                plugins: @js($plugin->getPlugins()),
                timeZone: @js($plugin->getTimezone()),
                config: @js($this->getConfig()),
                editable: @json($plugin->isEditable()),
                selectable: @json($plugin->isSelectable()),
                eventClassNames: {!! htmlspecialchars($this->eventClassNames(), ENT_COMPAT) !!},
                eventContent: {!! htmlspecialchars($this->eventContent(), ENT_COMPAT) !!},
                eventDidMount: {!! htmlspecialchars($this->eventDidMount(), ENT_COMPAT) !!},
                eventWillUnmount: {!! htmlspecialchars($this->eventWillUnmount(), ENT_COMPAT) !!},
            })"
        ></div>
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>
