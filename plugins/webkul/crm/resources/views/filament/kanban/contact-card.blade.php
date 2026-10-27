@php
    $url = $board->getRecordUrl($record);
    $location = collect([$record->city, $record->country?->name])->filter()->implode(', ');
@endphp

<div
    data-kanban-card
    data-record-id="{{ $record->getKey() }}"
    role="listitem"
    aria-label="{{ $record->name }}"
    x-on:click="if (! (typeof dragStarted !== 'undefined' && dragStarted)) window.location.href = @js($url)"
    class="fi-kanban-card group relative flex cursor-pointer gap-3 rounded-lg bg-white p-3 shadow-sm ring-1 ring-gray-950/5 transition-shadow hover:shadow-md dark:bg-gray-900 dark:ring-white/10"
>
    @include('crm::filament.contacts.avatar', ['contact' => $record, 'size' => 'sm'])

    <div class="min-w-0 flex-1">
        <h4 class="truncate text-sm font-semibold text-gray-950 dark:text-white">{{ $record->name }}</h4>

        @if ($record->parent)
            <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $record->parent->name }}</p>
        @endif

        @if ($record->email)
            <p class="mt-1 truncate text-xs text-gray-600 dark:text-gray-400">{{ $record->email }}</p>
        @endif

        @if ($location)
            <p class="truncate text-xs text-gray-600 dark:text-gray-400">{{ $location }}</p>
        @endif

        <div class="mt-2 flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
            <span class="flex items-center gap-1" title="{{ trans_choice('crm::filament/clusters/sales/resources/contact.grid.opportunities', (int) $record->crm_opportunities_count, ['count' => (int) $record->crm_opportunities_count]) }}">
                <x-filament::icon icon="heroicon-s-star" class="h-3.5 w-3.5" />
                {{ (int) $record->crm_opportunities_count }}
            </span>

            <span class="flex items-center gap-1" title="{{ trans_choice('crm::filament/clusters/sales/resources/contact.grid.sale-orders', (int) $record->crm_sale_orders_count, ['count' => (int) $record->crm_sale_orders_count]) }}">
                <x-filament::icon icon="heroicon-s-currency-dollar" class="h-3.5 w-3.5" />
                {{ (int) $record->crm_sale_orders_count }}
            </span>
        </div>
    </div>
</div>
