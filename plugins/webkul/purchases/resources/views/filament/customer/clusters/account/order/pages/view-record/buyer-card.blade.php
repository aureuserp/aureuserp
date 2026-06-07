<?php

$record = $this->getRecord() ?>

<div class="flex items-center justify-between gap-x-3">
    <dt class="fi-in-entry-wrp-label inline-flex items-center gap-x-3">
        <span class="text-md font-medium leading-6 text-gray-950 dark:text-white">
            {{ __('purchases::filament/customer/clusters/account/resources/order.infolist.settings.entries.buyer') }}
        </span>
    </dt>
</div>

<div class="mb-1 flex items-center text-sm">
    <x-filament-panels::avatar.user :user="$record->user" class="mr-2" size="w-12 h-12" />
    
    <div class="flex flex-col gap-1">
        <span class="font-medium">{{ $record->user->name }}</span>
        
        @if ($record->user->partner?->phone || $record->user->partner?->mobile)
            <a href="tel:{{ $record->user->partner->phone ?? $record->user->partner?->mobile }}" class="hover:text-primary-600 flex items-center text-gray-700">
                <x-filament::icon
                    icon="heroicon-m-phone"
                    class="mr-2 h-5 w-5"
                />

                {{ $record->user->partner->phone ?? $record->user->partner?->mobile }}
            </a>
        @endif
    </div>
</div>