<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filters --}}
        {{ $this->form }}

        {{-- Report Header --}}
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            @php
                $data = $this->generalLedgerData;
            @endphp
            
            @if(!empty($data))
                {{-- General Ledger Table --}}
                <div class="overflow-x-auto" x-data="{ expandedAccounts: [] }">
                    <table class="w-full text-sm">
                        <colgroup>
                            <col style="width: 50px;">
                            <col style="min-width: 250px;">
                            <col style="width: 120px;">
                            <col style="width: 180px;">
                            <col style="width: 180px;">
                            <col style="width: 120px; min-width: 120px;">
                            <col style="width: 120px; min-width: 120px;">
                            <col style="width: 120px; min-width: 120px;">
                        </colgroup>

                        <thead>
                            <tr class="border-b-2 border-gray-300 dark:border-gray-600">
                                <th class="px-4 py-2 text-left"></th>
                                <th class="px-4 py-2 text-left"></th>
                                <th class="px-4 py-2 text-left">Date</th>
                                <th class="px-4 py-2 text-left">Communication</th>
                                <th class="px-4 py-2 text-left">Partner</th>
                                <th class="px-4 py-2 text-right">Debit</th>
                                <th class="px-4 py-2 text-right">Credit</th>
                                <th class="px-4 py-2 text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalDebit = 0;
                                $totalCredit = 0;
                            @endphp

                            @foreach($data['accounts'] as $account)
                                @php
                                    $accountId = 'account-' . $account->id;
                                    $totalDebit += $account->period_debit;
                                    $totalCredit += $account->period_credit;
                                @endphp

                                {{-- Account Header Row --}}
                                <tr 
                                    class="cursor-pointer bg-gray-100 font-semibold hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600"
                                    @click="expandedAccounts.includes('{{ $accountId }}') ? expandedAccounts = expandedAccounts.filter(id => id !== '{{ $accountId }}') : expandedAccounts.push('{{ $accountId }}')"
                                >
                                    <td class="px-4 py-2" style="width: 50px;">
                                        <x-filament::icon
                                            icon="heroicon-m-chevron-right"
                                            class="inline-block h-4 w-4"
                                            x-show="!expandedAccounts.includes('{{ $accountId }}')"
                                        />
                                        <x-filament::icon
                                            icon="heroicon-m-chevron-down"
                                            class="inline-block h-4 w-4"
                                            x-show="expandedAccounts.includes('{{ $accountId }}')"
                                        />
                                    </td>
                                    <td class="px-4 py-2">
                                        {{ $account->code }} {{ $account->name }}
                                    </td>
                                    <td class="px-4 py-2"></td>
                                    <td class="px-4 py-2"></td>
                                    <td class="px-4 py-2"></td>
                                    <td class="px-4 py-2 text-right">{{ number_format($account->period_debit, 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($account->period_credit, 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($account->ending_balance, 2) }}</td>
                                </tr>

                                {{-- Account Moves (Hidden by default) --}}
                                @if($account->opening_balance != 0)
                                    <tr class="bg-blue-50 text-sm dark:bg-blue-900/20" x-show="expandedAccounts.includes('{{ $accountId }}')">
                                        <td class="px-4 py-1"></td>
                                        <td class="px-4 py-1 italic text-gray-600 dark:text-gray-300">Opening Balance</td>
                                        <td class="px-4 py-1 text-gray-600 dark:text-gray-300" style="white-space: nowrap;">
                                            {{ \Carbon\Carbon::parse($data['date_from'])->format('M d, Y') }}
                                        </td>
                                        <td class="px-4 py-1"></td>
                                        <td class="px-4 py-1"></td>
                                        <td class="px-4 py-1 text-right">
                                            {{ $account->opening_balance > 0 ? number_format($account->opening_balance, 2) : '' }}
                                        </td>
                                        <td class="px-4 py-1 text-right">
                                            {{ $account->opening_balance < 0 ? number_format(abs($account->opening_balance), 2) : '' }}
                                        </td>
                                        <td class="px-4 py-1 text-right font-semibold">{{ number_format($account->opening_balance, 2) }}</td>
                                    </tr>
                                @endif

                                @php
                                    $moves = $this->getAccountMoves($account->id);
                                    $runningBalance = $account->opening_balance;
                                @endphp

                                @foreach($moves as $move)
                                    @php
                                        $runningBalance += ($move['debit'] - $move['credit']);
                                    @endphp

                                    <tr class="border-t border-gray-100 text-sm hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700" x-show="expandedAccounts.includes('{{ $accountId }}')">
                                        <td class="px-4 py-1"></td>
                                        <td class="px-4 py-1 text-gray-600 dark:text-gray-300">
                                            {{ $move['move_name'] }}
                                            @if($move['ref'])
                                                <span class="text-xs text-gray-500">({{ $move['ref'] }})</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-1 text-gray-600 dark:text-gray-300" style="white-space: nowrap;">
                                            {{ \Carbon\Carbon::parse($move['date'])->format('M d, Y') }}
                                        </td>
                                        <td class="px-4 py-1 text-gray-600 dark:text-gray-300">
                                            @if ($move['move_type'] == 'entry')
                                                {{ $move['name'] }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-1 text-gray-600 dark:text-gray-300">{{ $move['partner_name'] }}</td>
                                        <td class="px-4 py-1 text-right">
                                            {{ $move['debit'] > 0 ? number_format($move['debit'], 2) : '' }}
                                        </td>
                                        <td class="px-4 py-1 text-right">
                                            {{ $move['credit'] > 0 ? number_format($move['credit'], 2) : '' }}
                                        </td>
                                        <td class="px-4 py-1 text-right font-medium">{{ number_format($runningBalance, 2) }}</td>
                                    </tr>
                                @endforeach
                            @endforeach

                            {{-- Total Row --}}
                            <tr class="border-t-4 border-gray-400 bg-gray-200 text-base font-bold dark:border-gray-500 dark:bg-gray-600">
                                <td class="px-4 py-3"></td>
                                <td class="px-4 py-3">Total</td>
                                <td class="px-4 py-3"></td>
                                <td class="px-4 py-3"></td>
                                <td class="px-4 py-3"></td>
                                <td class="px-4 py-3 text-right">{{ number_format($totalDebit, 2) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($totalCredit, 2) }}</td>
                                <td class="px-4 py-3 text-right"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">No data available for the selected filters.</p>
            @endif
        </div>
    </div>
</x-filament-panels::page>
