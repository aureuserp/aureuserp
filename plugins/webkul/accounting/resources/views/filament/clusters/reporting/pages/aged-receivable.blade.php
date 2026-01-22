<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="$refresh">
            {{ $this->form }}
        </form>

        @php
            $data = $this->agedReceivableData();
            $partners = $data['partners'];
            $asOfDate = $data['as_of_date'];
            $period = $data['period'];
            $hasUnposted = $data['has_unposted'];
        @endphp

        <x-filament::section>
            <x-slot name="heading">
                Aged Receivable - As of {{ $asOfDate->format('m/d/Y') }}
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
            
            @if($hasUnposted)
                <x-filament::badge color="warning" size="xl" class="mb-4 px-4 py-2 w-full justify-start text-sm!">
                    There are unposted Journal Entries prior or included in this period.
                </x-filament::badge>
            @endif

            @if(empty($partners))
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    No receivables found for the selected criteria.
                </div>
            @else
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-white/5!">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-white/5!">
                        <colgroup>
                            <col style="width: 50px;">
                            <col style="min-width: 250px;">
                            <col style="width: 140px;">
                            <col style="width: 140px;">
                            <col style="width: 120px; min-width: 120px;">
                            <col style="width: 120px; min-width: 120px;">
                            <col style="width: 120px; min-width: 120px;">
                            <col style="width: 120px; min-width: 120px;">
                            <col style="width: 120px; min-width: 120px;">
                            <col style="width: 120px; min-width: 120px;">
                        </colgroup>

                        <thead class="bg-gray-50/50 dark:bg-white/5">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400" style="width: 50px;"></th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Aged Receivable
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Invoice Date
                                </th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    At Date
                                </th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    1-{{ $period }}
                                </th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    {{ $period + 1 }}-{{ $period * 2 }}
                                </th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    {{ $period * 2 + 1 }}-{{ $period * 3 }}
                                </th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    {{ $period * 3 + 1 }}-{{ $period * 4 }}
                                </th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Older
                                </th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Total
                                </th>
                            </tr>
                        </thead>
                        
                        <tbody class="divide-y divide-gray-200 dark:divide-white/5!">
                            @php
                                $totals = [
                                    'at_date' => 0,
                                    'period_1' => 0,
                                    'period_2' => 0,
                                    'period_3' => 0,
                                    'period_4' => 0,
                                    'older' => 0,
                                    'total' => 0,
                                ];
                            @endphp

                            @foreach($partners as $partnerId => $partner)
                                <tbody wire:key="partner-{{ $partner['id'] }}" class="divide-y divide-gray-200 dark:divide-white/5!">
                                    {{-- Partner Header --}}
                                    <tr class="bg-gray-50/50 dark:bg-white/5 cursor-pointer hover:bg-gray-100/50 dark:hover:bg-white/5!"
                                        x-data="{ loading: false }"
                                        @click="loading = true; $wire.togglePartnerLines({{ $partner['id'] }}).then(() => loading = false)">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <svg x-show="!loading" class="w-4 h-4 transition-transform @if($this->isPartnerExpanded($partner['id'])) rotate-90 @endif" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                </svg>
                                                <x-filament::loading-indicator x-show="loading" x-cloak class="h-4 w-4" />
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="font-medium text-gray-900 dark:text-white">
                                                {{ $partner['partner_name'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3"></td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <span class="{{ $partner['at_date'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                                {{ $partner['at_date'] != 0 ? '$' . number_format($partner['at_date'], 2) : '' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <span class="{{ $partner['period_1'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                                {{ $partner['period_1'] != 0 ? '$' . number_format($partner['period_1'], 2) : '' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <span class="{{ $partner['period_2'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                                {{ $partner['period_2'] != 0 ? '$' . number_format($partner['period_2'], 2) : '' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <span class="{{ $partner['period_3'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                                {{ $partner['period_3'] != 0 ? '$' . number_format($partner['period_3'], 2) : '' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <span class="{{ $partner['period_4'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                                {{ $partner['period_4'] != 0 ? '$' . number_format($partner['period_4'], 2) : '' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <span class="{{ $partner['older'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                                {{ $partner['older'] != 0 ? '$' . number_format($partner['older'], 2) : '' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap font-semibold">
                                            <span class="{{ $partner['total'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                                ${{ number_format($partner['total'], 2) }}
                                            </span>
                                        </td>
                                    </tr>

                                    {{-- Partner Lines --}}
                                    @if($this->isPartnerExpanded($partner['id']))
                                        @foreach($this->getPartnerLines($partner['id']) as $line)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5!">
                                            <td class="px-4 py-2"></td>
                                            <td class="px-4 py-2 pl-12 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                                {{ $line['move_name'] }}
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                                {{ Carbon\Carbon::parse($line['invoice_date'])->format('m/d/Y') }}
                                            </td>
                                            <td class="px-4 py-2 text-right text-sm whitespace-nowrap">
                                                <span class="{{ $line['at_date'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $line['at_date'] != 0 ? '$' . number_format($line['at_date'], 2) : '' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 text-right text-sm whitespace-nowrap">
                                                <span class="{{ $line['period_1'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $line['period_1'] != 0 ? '$' . number_format($line['period_1'], 2) : '' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 text-right text-sm whitespace-nowrap">
                                                <span class="{{ $line['period_2'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $line['period_2'] != 0 ? '$' . number_format($line['period_2'], 2) : '' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 text-right text-sm whitespace-nowrap">
                                                <span class="{{ $line['period_3'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $line['period_3'] != 0 ? '$' . number_format($line['period_3'], 2) : '' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 text-right text-sm whitespace-nowrap">
                                                <span class="{{ $line['period_4'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $line['period_4'] != 0 ? '$' . number_format($line['period_4'], 2) : '' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 text-right text-sm whitespace-nowrap">
                                                <span class="{{ $line['older'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $line['older'] != 0 ? '$' . number_format($line['older'], 2) : '' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2"></td>
                                        </tr>
                                    @endforeach
                                    @endif

                                    @php
                                        $totals['at_date'] += $partner['at_date'];
                                        $totals['period_1'] += $partner['period_1'];
                                        $totals['period_2'] += $partner['period_2'];
                                        $totals['period_3'] += $partner['period_3'];
                                        $totals['period_4'] += $partner['period_4'];
                                        $totals['older'] += $partner['older'];
                                        $totals['total'] += $partner['total'];
                                    @endphp
                                </tbody>
                            @endforeach

                            <tbody>
                                {{-- Totals Row --}}
                                <tr class="bg-gray-100/80 dark:bg-white/5 font-semibold border-t-2 border-gray-300 dark:border-white/5!">
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-white">
                                        Total Aged Receivable
                                    </td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <span class="{{ $totals['at_date'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                            {{ $totals['at_date'] != 0 ? '$' . number_format($totals['at_date'], 2) : '' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <span class="{{ $totals['period_1'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                            {{ $totals['period_1'] != 0 ? '$' . number_format($totals['period_1'], 2) : '' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <span class="{{ $totals['period_2'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                            {{ $totals['period_2'] != 0 ? '$' . number_format($totals['period_2'], 2) : '' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <span class="{{ $totals['period_3'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                            {{ $totals['period_3'] != 0 ? '$' . number_format($totals['period_3'], 2) : '' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <span class="{{ $totals['period_4'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                            {{ $totals['period_4'] != 0 ? '$' . number_format($totals['period_4'], 2) : '' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <span class="{{ $totals['older'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                            {{ $totals['older'] != 0 ? '$' . number_format($totals['older'], 2) : '' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap text-gray-900 dark:text-white">
                                        ${{ number_format($totals['total'], 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
