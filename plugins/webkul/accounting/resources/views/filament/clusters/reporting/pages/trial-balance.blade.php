<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filters --}}
        {{ $this->form }}

        {{-- Report Header --}}
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            @php
                $data = $this->trialBalanceData;
            @endphp
            
            @if(!empty($data))
                {{-- Trial Balance Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <colgroup>
                            <col style="width: auto;">
                            <col style="width: 120px;">
                            <col style="width: 120px;">
                            <col style="width: 120px;">
                            <col style="width: 120px;">
                            <col style="width: 120px;">
                            <col style="width: 120px;">
                        </colgroup>
                        
                        <thead>
                            <tr class="border-b-2 border-gray-300 dark:border-gray-600">
                                <th class="px-4 py-2 text-left"></th>
                                <th colspan="2" class="border-b border-gray-200 px-4 py-2 text-center font-semibold dark:border-gray-600">Initial Balance</th>
                                <th colspan="2" class="border-b border-gray-200 px-4 py-2 text-center font-semibold dark:border-gray-600">{{ \Carbon\Carbon::parse($data['date_from'])->format('M Y') }}</th>
                                <th colspan="2" class="border-b border-gray-200 px-4 py-2 text-center font-semibold dark:border-gray-600">End Balance</th>
                            </tr>

                            <tr class="border-b border-gray-300 dark:border-gray-600">
                                <th class="px-4 py-2 text-left"></th>
                                <th class="px-4 py-2 text-right text-xs">Debit</th>
                                <th class="px-4 py-2 text-right text-xs">Credit</th>
                                <th class="px-4 py-2 text-right text-xs">Debit</th>
                                <th class="px-4 py-2 text-right text-xs">Credit</th>
                                <th class="px-4 py-2 text-right text-xs">Debit</th>
                                <th class="px-4 py-2 text-right text-xs">Credit</th>
                            </tr>
                        </thead>

                        <tbody>
                            @if(count($data['accounts']) > 0)
                                @foreach($data['accounts'] as $account)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-1 text-gray-900 dark:text-gray-100">
                                            {{ $account->code ? $account->code . ' ' : '' }}{{ $account->name }}
                                        </td>
                                        <td class="px-4 py-1 text-right text-gray-900 dark:text-gray-100">
                                            {{ $account->initial_debit > 0 ? number_format($account->initial_debit, 2) : '0.00' }}
                                        </td>
                                        <td class="px-4 py-1 text-right text-gray-900 dark:text-gray-100">
                                            {{ $account->initial_credit > 0 ? number_format($account->initial_credit, 2) : '0.00' }}
                                        </td>
                                        <td class="px-4 py-1 text-right text-gray-900 dark:text-gray-100">
                                            {{ $account->period_debit > 0 ? number_format($account->period_debit, 2) : '0.00' }}
                                        </td>
                                        <td class="px-4 py-1 text-right text-gray-900 dark:text-gray-100">
                                            {{ $account->period_credit > 0 ? number_format($account->period_credit, 2) : '0.00' }}
                                        </td>
                                        <td class="px-4 py-1 text-right text-gray-900 dark:text-gray-100">
                                            {{ $account->end_debit > 0 ? number_format($account->end_debit, 2) : '0.00' }}
                                        </td>
                                        <td class="px-4 py-1 text-right text-gray-900 dark:text-gray-100">
                                            {{ $account->end_credit > 0 ? number_format($account->end_credit, 2) : '0.00' }}
                                        </td>
                                    </tr>
                                @endforeach
                                
                                {{-- Total Row --}}
                                <tr class="border-t-2 border-gray-300 bg-gray-50 font-bold dark:border-gray-600 dark:bg-gray-700">
                                    <td class="px-4 py-2">Total</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($data['totals']['initial_debit'], 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($data['totals']['initial_credit'], 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($data['totals']['period_debit'], 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($data['totals']['period_credit'], 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($data['totals']['end_debit'], 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($data['totals']['end_credit'], 2) }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="7" class="py-8 text-center italic text-gray-500 dark:text-gray-400">
                                        No accounts with transactions in this period
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-12 text-center">
                    <div class="mb-2 text-gray-400 dark:text-gray-600">
                        <x-filament::icon
                            icon="heroicon-o-scale"
                            class="mx-auto h-16 w-16"
                        />
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No data available for the selected period</p>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
