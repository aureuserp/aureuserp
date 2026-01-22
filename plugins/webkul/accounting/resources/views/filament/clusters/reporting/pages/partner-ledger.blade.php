<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filters --}}
        {{ $this->form }}

        {{-- Report Header --}}
        <x-filament::section>
            @php
                $data = $this->partnerLedgerData;
            @endphp
            
            <x-slot name="heading">
                Partner Ledger - From {{ \Carbon\Carbon::parse($data['date_from'])->format('M d, Y') }} to {{ \Carbon\Carbon::parse($data['date_to'])->format('M d, Y') }}
            </x-slot>

            <x-slot name="afterHeader">
                <div class="flex gap-2">
                    <x-filament::link
                        wire:click="expandAll"
                        tag="button"
                        size="sm"
                        color="primary"
                    >
                        Expand All
                    </x-filament::link>

                    <span class="text-gray-500 dark:text-gray-400 flex items-center">/</span>
                    
                    <x-filament::link
                        wire:click="collapseAll"
                        tag="button"
                        size="sm"
                        color="primary"
                    >
                        Collapse All
                    </x-filament::link>
                </div>
            </x-slot>
            
            {{-- Partner Ledger Table --}}
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-white/5!">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-white/5!">
                    <colgroup>
                        <col style="width: 50px;">
                        <col style="min-width: 250px;">
                        <col style="width: 180px;">
                        <col style="width: 180px;">
                        <col style="width: 140px;">
                        <col style="width: 140px;">
                        <col style="width: 120px; min-width: 120px;">
                        <col style="width: 120px; min-width: 120px;">
                        <col style="width: 120px; min-width: 120px;">
                    </colgroup>

                    <thead class="bg-gray-50/50 dark:bg-white/5">
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
                    
                    @php
                        $totalDebit = 0;
                        $totalCredit = 0;
                    @endphp

                    @if($data['partners']->isNotEmpty())
                        @foreach($data['partners'] as $partner)
                            @php
                                $totalDebit += $partner->period_debit;
                                $totalCredit += $partner->period_credit;
                            @endphp
                            
                            <tbody wire:key="partner-{{ $partner->id }}" class="divide-y divide-gray-200 dark:divide-white/5!">
                                {{-- Partner Header Row --}}
                                    <tr 
                                        class="bg-gray-50/50 dark:bg-white/5 cursor-pointer hover:bg-gray-100/50 dark:hover:bg-white/5!"
                                        x-data="{ loading: false }"
                                        @click="loading = true; $wire.togglePartnerLines({{ $partner->id }}).then(() => loading = false)"
                                    >
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <svg x-show="!loading" class="w-4 h-4 transition-transform @if($this->isPartnerExpanded($partner->id)) rotate-90 @endif" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                </svg>
                                                <x-filament::loading-indicator x-show="loading" x-cloak class="h-4 w-4" />
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="font-medium text-gray-900 dark:text-white">
                                                {{ $partner->name }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3"></td>
                                        <td class="px-4 py-3"></td>
                                        <td class="px-4 py-3"></td>
                                        <td class="px-4 py-3"></td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <span class="text-gray-900 dark:text-white">
                                                {{ number_format($partner->period_debit, 2) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <span class="text-gray-900 dark:text-white">
                                                {{ number_format($partner->period_credit, 2) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap font-semibold">
                                            <span class="text-gray-900 dark:text-white">
                                                {{ number_format($partner->ending_balance, 2) }}
                                            </span>
                                        </td>
                                    </tr>

                                    {{-- Opening Balance Row --}}
                                    @if($partner->opening_balance != 0 && $this->isPartnerExpanded($partner->id))
                                        <tr class="bg-white dark:bg-gray-900">
                                            <td class="px-4 py-2"></td>
                                            <td class="px-4 py-2 pl-8 whitespace-nowrap text-sm">
                                                <span class="italic text-gray-600 dark:text-gray-400">
                                                    Opening Balance
                                                </span>
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
                                        $runningBalance = $partner->opening_balance;
                                    @endphp

                                    @if($this->isPartnerExpanded($partner->id))
                                        @foreach($this->getPartnerMoves($partner->id) as $move)
                                            @php
                                                $runningBalance += ($move['debit'] - $move['credit']);
                                            @endphp
                                            
                                            <tr class="bg-white dark:bg-gray-900">
                                            <td class="px-4 py-2"></td>
                                            <td class="px-4 py-2 pl-8 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                                {{ $move['move_name'] }}

                                                @if($move['ref'])
                                                    <span class="text-xs text-gray-500 dark:text-gray-500">
                                                        ({{ $move['ref'] }})
                                                    </span>
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
                                                <span class="font-medium text-gray-600 dark:text-gray-400">
                                                    {{ number_format($runningBalance, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            @endforeach

                            <tbody>
                                {{-- Total Row --}}
                                <tr class="bg-gray-100/80 dark:bg-white/5 font-semibold border-t-2 border-gray-300 dark:border-white/5!">
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-white">
                                        Total Partner Ledger
                                    </td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap text-gray-900 dark:text-white">
                                        {{ number_format($totalDebit, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap text-gray-900 dark:text-white">
                                        {{ number_format($totalCredit, 2) }}
                                    </td>
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
        </x-filament::section>
    </div>
</x-filament-panels::page>
