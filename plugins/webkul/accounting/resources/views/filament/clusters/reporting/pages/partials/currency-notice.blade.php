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
                Consolidated across {{ $notice['company_count'] }} companies, shown in {{ $notice['currency'] }}.
            </p>
        @endif

        @if ($notice['missing_rates'])
            <p @class(['mt-1' => $notice['consolidated']])>
                No exchange rate found for
                {{ implode(', ', $notice['missing_companies']) }}.
                Amounts from
                {{ count($notice['missing_companies']) === 1 ? 'this company' : 'these companies' }}
                are included untranslated and the totals below are not reliable.
            </p>
        @endif
    </div>
@endif
