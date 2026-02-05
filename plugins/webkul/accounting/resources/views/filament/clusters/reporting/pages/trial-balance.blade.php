<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filters --}}
        {{ $this->form }}

        {{-- Report Header --}}
        <x-filament::section>
            @php
                $data = $this->trialBalanceData;
            @endphp
            
            {{-- Trial Balance Table --}}
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-white/5!">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-white/5!">
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
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"></th>
                            <th colspan="2" scope="col" class="border-b border-gray-200 px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:border-white/5! dark:text-gray-400">
                                Initial Balance
                            </th>
                            <th colspan="2" scope="col" class="border-b border-gray-200 px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:border-white/5! dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($data['date_from'])->format('d M Y').' - '.\Carbon\Carbon::parse($data['date_to'])->format('d M Y') }}
                            </th>
                            <th colspan="2" scope="col" class="border-b border-gray-200 px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:border-white/5! dark:text-gray-400">
                                End Balance
                            </th>
                        </tr>

                        <tr class="border-b border-gray-200 dark:border-white/5!">
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Account
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Debit
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Credit
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Debit
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Credit
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Debit
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Credit
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-white/5!">
                        @if($data['accounts']->isNotEmpty())
                            @foreach($data['accounts'] as $account)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5!">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100!">
                                        {{ $account->code ? $account->code . ' ' : '' }}{{ $account->name }}
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap text-sm text-gray-900 dark:text-gray-100!">
                                        {{ $account->initial_debit > 0 ? number_format($account->initial_debit, 2) : '0.00' }}
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap text-sm text-gray-900 dark:text-gray-100!">
                                        {{ $account->initial_credit > 0 ? number_format($account->initial_credit, 2) : '0.00' }}
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap text-sm text-gray-900 dark:text-gray-100!">
                                        {{ $account->period_debit > 0 ? number_format($account->period_debit, 2) : '0.00' }}
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap text-sm text-gray-900 dark:text-gray-100!">
                                        {{ $account->period_credit > 0 ? number_format($account->period_credit, 2) : '0.00' }}
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap text-sm text-gray-900 dark:text-gray-100!">
                                        {{ $account->end_debit > 0 ? number_format($account->end_debit, 2) : '0.00' }}
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap text-sm text-gray-900 dark:text-gray-100!">
                                        {{ $account->end_credit > 0 ? number_format($account->end_credit, 2) : '0.00' }}
                                    </td>
                                </tr>
                            @endforeach
                            
                            {{-- Total Row --}}
                            <tr class="bg-gray-100/80 dark:bg-white/5 font-semibold border-t-2 border-gray-300 dark:border-white/5!">
                                <td class="px-4 py-3 text-gray-900 dark:text-white">
                                    Total
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap text-gray-900 dark:text-white">
                                    {{ number_format($data['totals']['initial_debit'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap text-gray-900 dark:text-white">
                                    {{ number_format($data['totals']['initial_credit'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap text-gray-900 dark:text-white">
                                    {{ number_format($data['totals']['period_debit'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap text-gray-900 dark:text-white">
                                    {{ number_format($data['totals']['period_credit'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap text-gray-900 dark:text-white">
                                    {{ number_format($data['totals']['end_debit'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap text-gray-900 dark:text-white">
                                    {{ number_format($data['totals']['end_credit'], 2) }}
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No accounts with transactions in this period
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
