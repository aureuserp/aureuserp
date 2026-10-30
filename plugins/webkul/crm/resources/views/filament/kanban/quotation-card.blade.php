@php
    $recordId = $record->getKey();
    $url = $board->getRecordUrl($record);
    $salesperson = $record->user;
    $initials = $salesperson ? collect(explode(' ', $salesperson->name))->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('') : null;
@endphp

<div
    wire:key="kanban-card-{{ $recordId }}"
    data-kanban-card
    data-record-id="{{ $recordId }}"
    role="listitem"
    aria-label="{{ $record->name }}"
    title="{{ __('crm::filament/clusters/sales/resources/quotation.board.card.open', ['name' => $record->name]) }}"
    x-on:click="if (! (typeof dragStarted !== 'undefined' && dragStarted)) window.location.href = @js($url)"
    class="fi-kanban-card group relative cursor-pointer rounded-lg bg-white p-3 shadow-sm ring-1 ring-gray-950/5 transition-shadow hover:shadow-md dark:bg-gray-900 dark:ring-white/10"
>
    <div class="flex items-start justify-between gap-2">
        <h4 class="text-sm font-semibold text-gray-950 dark:text-white">{{ $record->name }}</h4>

        @if ($record->state)
            <x-filament::badge size="sm" :color="$record->state->getColor()">
                {{ $record->state->getLabel() }}
            </x-filament::badge>
        @endif
    </div>

    @if ($record->partner)
        <p class="mt-1 flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-400" title="{{ __('crm::filament/clusters/sales/resources/quotation.board.card.customer') }}">
            <x-filament::icon icon="heroicon-m-building-office-2" class="h-3.5 w-3.5 shrink-0 text-gray-400" />
            <span class="truncate">{{ $record->partner->name }}</span>
        </p>
    @endif

    <div class="mt-2.5 flex items-center justify-between gap-2">
        <div class="min-w-0">
            <p class="text-sm font-medium text-gray-950 dark:text-white" title="{{ __('crm::filament/clusters/sales/resources/quotation.board.card.total') }}">
                {{ money((float) $record->amount_total, $record->currency?->name) }}
            </p>

            @if ($record->date_order)
                <p class="text-xs text-gray-500 dark:text-gray-400" title="{{ __('crm::filament/clusters/sales/resources/quotation.board.card.date') }}">
                    {{ $record->date_order->toFormattedDateString() }}
                </p>
            @endif
        </div>

        @if ($initials)
            <span
                class="crm-kanban-avatar flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[10px] font-semibold"
                title="{{ __('crm::filament/clusters/sales/resources/quotation.board.card.salesperson') }}: {{ $salesperson->name }}"
            >
                {{ $initials }}
            </span>
        @endif
    </div>
</div>
