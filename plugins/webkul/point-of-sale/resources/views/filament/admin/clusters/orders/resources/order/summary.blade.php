@php
    $record = $getRecord();
    $prefix = 'point-of-sale::filament/admin/clusters/orders/resources/order.form.tabs.products.summary.';
    $currency = $record?->currency?->name;
@endphp

@if ($record)
    <div class="flex justify-end">
        <div class="w-full max-w-sm rounded-xl bg-white p-5 dark:border dark:border-gray-700 dark:bg-gray-900">
            <div class="flex items-center justify-between py-2 text-sm text-gray-600 dark:text-gray-300">
                <span>{{ __($prefix.'untaxed') }}</span>
                <span class="font-semibold tabular-nums">{{ money((float) $record->amount_untaxed, $currency) }}</span>
            </div>

            @if ((float) $record->amount_tax)
                <div class="flex items-center justify-between py-2 text-sm text-gray-600 dark:text-gray-300">
                    <span>{{ __($prefix.'taxes') }}</span>
                    <span class="font-semibold tabular-nums">{{ money((float) $record->amount_tax, $currency) }}</span>
                </div>
            @endif

            @if ((float) $record->amount_rounding)
                <div class="flex items-center justify-between py-2 text-sm text-gray-600 dark:text-gray-300">
                    <span>{{ __($prefix.'rounding') }}</span>
                    <span class="font-semibold tabular-nums">{{ money((float) $record->amount_rounding, $currency) }}</span>
                </div>
            @endif

            <div class="my-3 border-b border-gray-200 dark:border-gray-700"></div>

            <div class="flex items-center justify-between py-2 text-sm font-bold text-gray-950 dark:text-white">
                <span>{{ __($prefix.'total') }}</span>
                <span class="tabular-nums">{{ money((float) $record->amount_total, $currency) }}</span>
            </div>

            <div class="flex items-center justify-between py-2 text-sm text-gray-600 dark:text-gray-300">
                <span>{{ __($prefix.'paid') }}</span>
                <span class="font-semibold tabular-nums">{{ money((float) $record->amount_paid, $currency) }}</span>
            </div>

            @if ((float) $record->amount_return)
                <div class="flex items-center justify-between py-2 text-sm text-gray-600 dark:text-gray-300">
                    <span>{{ __($prefix.'change') }}</span>
                    <span class="font-semibold tabular-nums">{{ money((float) $record->amount_return, $currency) }}</span>
                </div>
            @endif
        </div>
    </div>
@endif
