<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filters --}}
        {{ $this->form }}

        {{-- Report Header --}}
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            @php
                $data = $this->partnerLedgerData;
            @endphp
            
            @if(!empty($data))
                {{-- Partner Ledger Table --}}
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <colgroup>
                            <col style="width: 50px;">
                            <col style="min-width: 250px;">
                            <col style="width: 180px;">
                            <col style="width: 180px;">
                            <col style="width: 120px;">
                            <col style="width: 120px;">
                            <col style="width: 120px; min-width: 120px;">
                            <col style="width: 120px; min-width: 120px;">
                            <col style="width: 120px; min-width: 120px;">
                        </colgroup>
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"></th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Partner</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Journal</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Account</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Invoice Date</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Due Date</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Debit</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Credit</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                            @php
                                $totalDebit = 0;
                                $totalCredit = 0;
                            @endphp

                            @if(count($data['partners']) > 0)
                                @foreach($data['partners'] as $partner)
                                    @php
                                        $totalDebit += $partner->period_debit;
                                        $totalCredit += $partner->period_credit;
                                    @endphp
                                    
                                    <tbody x-data="{ expanded: false }">
                                        {{-- Partner Header Row --}}
                                        <tr 
                                            class="bg-gray-50 dark:bg-gray-800 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
                                            @click="expanded = !expanded"
                                        >
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-90': expanded }" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $partner->name }}</span>
                                            </td>
                                            <td class="px-4 py-3"></td>
                                            <td class="px-4 py-3"></td>
                                            <td class="px-4 py-3"></td>
                                            <td class="px-4 py-3"></td>
                                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                                <span class="text-gray-900 dark:text-white">{{ number_format($partner->period_debit, 2) }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                                <span class="text-gray-900 dark:text-white">{{ number_format($partner->period_credit, 2) }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-right whitespace-nowrap font-semibold">
                                                <span class="text-gray-900 dark:text-white">{{ number_format($partner->ending_balance, 2) }}</span>
                                            </td>
                                        </tr>

                                        {{-- Opening Balance Row --}}
                                        @if($partner->opening_balance != 0)
                                            <tr x-show="expanded" x-cloak class="bg-white dark:bg-gray-900">
                                                <td class="px-4 py-2"></td>
                                                <td class="px-4 py-2 pl-8 whitespace-nowrap text-sm">
                                                    <span class="italic text-gray-600 dark:text-gray-400">Opening Balance</span>
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                                    {{ \Carbon\Carbon::parse($data['date_from'])->format('M d, Y') }}
                                                </td>
                                                <td class="px-4 py-2"></td>
                                                <td class="px-4 py-2"></td>
                                                <td class="px-4 py-2"></td>
                                                <td class="px-4 py-2 text-right text-sm whitespace-nowrap">
                                                    <span class="text-gray-600 dark:text-gray-400">
                                                        {{ $partner->opening_balance > 0 ? number_format($partner->opening_balance, 2) : '' }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2 text-right text-sm whitespace-nowrap">
                                                    <span class="text-gray-600 dark:text-gray-400">
                                                        {{ $partner->opening_balance < 0 ? number_format(abs($partner->opening_balance), 2) : '' }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2 text-right text-sm whitespace-nowrap">
                                                    <span class="font-semibold text-gray-600 dark:text-gray-400">{{ number_format($partner->opening_balance, 2) }}</span>
                                                </td>
                                            </tr>
                                        @endif

                                        @php
                                            $moves = $this->getPartnerMoves($partner->id);
                                            $runningBalance = $partner->opening_balance;
                                        @endphp

                                        @foreach($moves as $move)
                                            @php
                                                $runningBalance += ($move['debit'] - $move['credit']);
                                            @endphp
                                            <tr x-show="expanded" x-cloak class="bg-white dark:bg-gray-900">
                                                <td class="px-4 py-2"></td>
                                                <td class="px-4 py-2 pl-8 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $move['move_name'] }}
                                                    @if($move['ref'])
                                                        <span class="text-xs text-gray-500 dark:text-gray-500">({{ $move['ref'] }})</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $move['journal_name'] }}
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                                    @if($move['account_code'])
                                                        {{ $move['account_code'] }} {{ $move['account_name'] }}
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                                    {{ \Carbon\Carbon::parse($move['invoice_date'])->format('M d, Y') }}
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                                    {{ \Carbon\Carbon::parse($move['invoice_date_due'])->format('M d, Y') }}
                                                </td>
                                                <td class="px-4 py-2 text-right text-sm whitespace-nowrap">
                                                    <span class="text-gray-600 dark:text-gray-400">
                                                        {{ $move['debit'] > 0 ? number_format($move['debit'], 2) : '' }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2 text-right text-sm whitespace-nowrap">
                                                    <span class="text-gray-600 dark:text-gray-400">
                                                        {{ $move['credit'] > 0 ? number_format($move['credit'], 2) : '' }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2 text-right text-sm whitespace-nowrap">
                                                    <span class="font-medium text-gray-600 dark:text-gray-400">{{ number_format($runningBalance, 2) }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                @endforeach

                                <tbody>
                                    {{-- Total Row --}}
                                    <tr class="bg-gray-100 dark:bg-gray-800 font-semibold border-t-2 border-gray-300 dark:border-gray-600">
                                        <td class="px-4 py-3"></td>
                                        <td class="px-4 py-3 text-gray-900 dark:text-white">Total Partner Ledger</td>
                                        <td class="px-4 py-3"></td>
                                        <td class="px-4 py-3"></td>
                                        <td class="px-4 py-3"></td>
                                        <td class="px-4 py-3"></td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap text-gray-900 dark:text-white">{{ number_format($totalDebit, 2) }}</td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap text-gray-900 dark:text-white">{{ number_format($totalCredit, 2) }}</td>
                                        <td class="px-4 py-3"></td>
                                    </tr>
                                </tbody>
                            @else
                                <tbody>
                                    <tr>
                                        <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No partners with transactions in this period
                                        </td>
                                    </tr>
                                </tbody>
                            @endif
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">No data available for the selected filters.</p>
            @endif
        </div>
    </div>
</x-filament-panels::page>
