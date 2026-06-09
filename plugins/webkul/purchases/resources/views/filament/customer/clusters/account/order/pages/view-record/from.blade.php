<?php

$record = $this->getRecord() ?>

<div class="flex items-center justify-between gap-x-3">
    <dt class="fi-in-entry-wrp-label inline-flex items-center gap-x-3">
        <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
            {{ __('purchases::filament/customer/clusters/account/resources/order.infolist.general.entries.from') }}
        </span>
    </dt>
</div>

<div class="">
    <p class="mt-2 text-sm leading-6 text-gray-950 dark:text-white">
        {{ $record->company->name }}

        @if ($record->company->partner)
            ({{ $record->company->partner->city }})
        @endif
    </p>

    @if ($record->company->partner)
        <p class="mt-2 text-sm leading-6 text-gray-950 dark:text-white">
            {{ $record->company->partner->street1 }}

            @if ($record->company->partner->street2)
                ,{{ $record->company->partner->street2 }}
            @endif
        </p>
        
        <p class="text-sm leading-6 text-gray-950 dark:text-white">
            {{ $record->company->partner->city }},

            @if ($record->company->partner->state)
                {{ $record->company->partner->state->name }},
            @endif
            
            {{ $record->company->partner->zip }}
        </p>
        
        @if ($record->company->partner->country)
            <p class="text-sm leading-6 text-gray-950 dark:text-white">
                {{ $record->company->partner->country->name }}
            </p>
        @endif
    @endif
</div>