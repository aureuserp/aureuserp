<x-filament-panels::page>
    <div class="space-y-4">
        {{-- Filters --}}
        {{ $this->form }}

        @include('accounting::filament.clusters.reporting.pages.partials.currency-notice')

        {{-- Report Header --}}
        <x-filament::section>
            @php
                $data = $this->trialBalanceData;
            @endphp
            
            {{-- Trial Balance Table --}}
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-white/5!">
                <table class="text-sm min-w-full divide-y divide-gray-200 dark:divide-white/5!">
                    <colgroup>
                        <col style="width: auto;">
                        <col style="width: 120px;">
                        <col style="width: 120px;">
                        <col style="width: 120px;">
                        <col style="width: 120px;">
                        <col style="width: 120px;">
                        <col style="width: 120px;">
                    </colgroup>
                    
                    <thead class="bg-gray-50/50 dark:bg-white/5">
                        <tr>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"></th>
                            <th colspan="2" scope="col" class="border-b border-gray-200 px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:border-white/5! dark:text-gray-400">
                                {{ __('accounting::filament/clusters/reporting.common.initial-balance') }}
                            </th>
                            <th colspan="2" scope="col" class="border-b border-gray-200 px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:border-white/5! dark:text-gray-400">
                                {{ __('accounting::filament/clusters/reporting/pages/trial-balance.date-range', ['from' => format_date($data['date_from']), 'to' => format_date($data['date_to'])]) }}
                            </th>
                            <th colspan="2" scope="col" class="border-b border-gray-200 px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:border-white/5! dark:text-gray-400">
                                {{ __('accounting::filament/clusters/reporting.common.end-balance') }}
                            </th>
                        </tr>

                        <tr class="border-b border-gray-200 dark:border-white/5!">
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                {{ __('accounting::filament/clusters/reporting.common.account') }}
                            </th>
                            <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                {{ __('accounting::filament/clusters/reporting.common.debit') }}
                            </th>
                            <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                {{ __('accounting::filament/clusters/reporting.common.credit') }}
                            </th>
                            <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                {{ __('accounting::filament/clusters/reporting.common.debit') }}
                            </th>
                            <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                {{ __('accounting::filament/clusters/reporting.common.credit') }}
                            </th>
                            <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                {{ __('accounting::filament/clusters/reporting.common.debit') }}
                            </th>
                            <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                {{ __('accounting::filament/clusters/reporting.common.credit') }}
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-white/5!">
                        @if($data['accounts']->isNotEmpty())
                            @foreach($data['accounts'] as $account)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5!">
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100!">
                                        {{ $account->code ? $account->code . ' ' : '' }}{{ $account->name }}
                                    </td>
                                    <td class="px-3 py-2 text-right whitespace-nowrap text-sm text-gray-900 dark:text-gray-100!">
                                        {{ $account->initial_debit > 0 ? format_number($account->initial_debit) : format_number(0) }}
                                    </td>
                                    <td class="px-3 py-2 text-right whitespace-nowrap text-sm text-gray-900 dark:text-gray-100!">
                                        {{ $account->initial_credit > 0 ? format_number($account->initial_credit) : format_number(0) }}
                                    </td>
                                    <td class="px-3 py-2 text-right whitespace-nowrap text-sm text-gray-900 dark:text-gray-100!">
                                        {{ $account->period_debit > 0 ? format_number($account->period_debit) : format_number(0) }}
                                    </td>
                                    <td class="px-3 py-2 text-right whitespace-nowrap text-sm text-gray-900 dark:text-gray-100!">
                                        {{ $account->period_credit > 0 ? format_number($account->period_credit) : format_number(0) }}
                                    </td>
                                    <td class="px-3 py-2 text-right whitespace-nowrap text-sm text-gray-900 dark:text-gray-100!">
                                        {{ $account->end_debit > 0 ? format_number($account->end_debit) : format_number(0) }}
                                    </td>
                                    <td class="px-3 py-2 text-right whitespace-nowrap text-sm text-gray-900 dark:text-gray-100!">
                                        {{ $account->end_credit > 0 ? format_number($account->end_credit) : format_number(0) }}
                                    </td>
                                </tr>
                            @endforeach
                            
                            {{-- Total Row --}}
                            <tr class="bg-gray-100/80 dark:bg-white/5 font-semibold border-t-2 border-gray-300 dark:border-white/5!">
                                <td class="px-3 py-2 text-gray-900 dark:text-white">
                                    {{ __('accounting::filament/clusters/reporting.common.total') }}
                                </td>
                                <td class="px-3 py-2 text-right whitespace-nowrap text-gray-900 dark:text-white">
                                    {{ format_number($data['totals']['initial_debit']) }}
                                </td>
                                <td class="px-3 py-2 text-right whitespace-nowrap text-gray-900 dark:text-white">
                                    {{ format_number($data['totals']['initial_credit']) }}
                                </td>
                                <td class="px-3 py-2 text-right whitespace-nowrap text-gray-900 dark:text-white">
                                    {{ format_number($data['totals']['period_debit']) }}
                                </td>
                                <td class="px-3 py-2 text-right whitespace-nowrap text-gray-900 dark:text-white">
                                    {{ format_number($data['totals']['period_credit']) }}
                                </td>
                                <td class="px-3 py-2 text-right whitespace-nowrap text-gray-900 dark:text-white">
                                    {{ format_number($data['totals']['end_debit']) }}
                                </td>
                                <td class="px-3 py-2 text-right whitespace-nowrap text-gray-900 dark:text-white">
                                    {{ format_number($data['totals']['end_credit']) }}
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="7" class="px-3 py-6 text-center text-gray-500 dark:text-gray-400">
                                    {{ __('accounting::filament/clusters/reporting.common.no-accounts-transactions') }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
