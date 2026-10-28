@php
    $notice = $this->currencyNotice();
@endphp

@if ($notice)
    <div @class([
        'rounded-lg border p-4 text-sm',
        'border-warning-300 bg-warning-50 text-warning-800 dark:border-warning-400/30 dark:bg-warning-400/10 dark:text-warning-300' => $notice['missing_rates'],
        'border-gray-200 bg-gray-50 text-gray-700 dark:border-white/10 dark:bg-white/5 dark:text-gray-300' => ! $notice['missing_rates'],
    ])>
        @if ($notice['consolidated'])
            <p class="font-medium">
                {{ __('accounting::filament/clusters/reporting.common.currency-notice.consolidated', ['count' => $notice['company_count'], 'currency' => $notice['currency']]) }}
            </p>
        @endif

        @if ($notice['missing_rates'])
            <p @class(['mt-1' => $notice['consolidated']])>
                {{ trans_choice('accounting::filament/clusters/reporting.common.currency-notice.missing-rates', count($notice['missing_companies']), ['companies' => implode(', ', $notice['missing_companies'])]) }}
            </p>
        @endif
    </div>
@endif
