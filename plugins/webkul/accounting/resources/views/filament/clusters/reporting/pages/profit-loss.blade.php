<x-filament-panels::page>
    <div class="space-y-4">
        {{-- Filters --}}
        {{ $this->form }}

        @include('accounting::filament.clusters.reporting.pages.partials.currency-notice')

        {{-- Report Header --}}
        <x-filament::section>
            @php
                $data = $this->profitLossData;
                $currency = $this->reportCurrency();
            @endphp
            
            <x-slot name="heading">
                {{ __('accounting::filament/clusters/reporting.common.from-to', [
                    'report' => __('accounting::filament/clusters/reporting.pages.profit-loss.navigation.title'),
                    'from'   => format_date($data['date_from']),
                    'to'     => format_date($data['date_to']),
                ]) }}
            </x-slot>
            
            {{-- Profit & Loss Table --}}
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-white/5!">
                <table class="text-sm min-w-full divide-y divide-gray-200 dark:divide-white/5!">
                    <thead class="bg-gray-50/50 dark:bg-white/5">
                        <tr>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                {{ __('accounting::filament/clusters/reporting.common.account') }}
                            </th>
                            <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                {{ __('accounting::filament/clusters/reporting/pages/profit-loss.table.columns.amount') }}
                            </th>
                        </tr>
                    </thead>
                    
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5!">
                        @foreach($data['sections'] as $sectionIndex => $section)
                            {{-- Section Header --}}
                            <tr class="bg-gray-100/80 dark:bg-white/5">
                                <td colspan="2" class="px-3 py-2 text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $section['title'] }}
                                </td>
                            </tr>

                            {{-- Accounts --}}
                            @if(count($section['accounts']) > 0)
                                @foreach($section['accounts'] as $account)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5!">
                                        <td class="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400" style="padding-left: 2rem;">
                                            {{ $account['code'] ? $account['code'] . ' - ' : '' }}{{ $account['name'] }}
                                        </td>
                                        <td class="px-3 py-1.5 text-right text-sm {{ isset($section['is_expense']) && $section['is_expense'] ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100!' }}">
                                            {{ money($account['balance'], $currency) }}
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Section Total --}}
                                <tr class="border-t border-gray-200 font-semibold bg-gray-50/50 dark:border-gray-700 dark:bg-gray-900/50">
                                    <td class="px-3 py-1.5 text-gray-900 dark:text-white">{{ $section['total_label'] }}</td>
                                    <td class="px-3 py-1.5 text-right {{ isset($section['is_expense']) && $section['is_expense'] ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                        {{ money($section['total'], $currency) }}
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="2" class="px-3 py-1.5 text-sm italic text-gray-500 dark:text-gray-400" style="padding-left: 2rem;">
                                        {{ $section['empty_message'] }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                        {{-- NET INCOME/LOSS --}}
                        <tr class="border-t-2 border-gray-300 font-bold bg-gray-100/80 dark:border-white/5! dark:bg-white/5">
                            <td class="px-3 py-2 text-gray-900 dark:text-white">{{ $data['is_profit'] ? __('accounting::filament/clusters/reporting/pages/profit-loss.totals.net-profit') : __('accounting::filament/clusters/reporting/pages/profit-loss.totals.net-loss') }}</td>
                            <td class="px-3 py-2 text-right {{ $data['is_profit'] ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ money(abs($data['net_income']), $currency) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
