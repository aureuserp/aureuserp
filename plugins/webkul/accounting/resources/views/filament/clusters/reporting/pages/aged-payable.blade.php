<x-filament-panels::page>
    <div>
        <form wire:submit="$refresh">
            {{ $this->form }}
        </form>

        @php
            $data = $this->agedPayableData();
            $partners = $data['partners'];
            $asOfDate = $data['as_of_date'];
            $period = $data['period'];
            $hasUnposted = $data['has_unposted'];
        @endphp

        @if($hasUnposted)
            <div class="mt-4 p-4 bg-warning-50 border border-warning-200 rounded-lg dark:bg-warning-900/20 dark:border-warning-800">
                <p class="text-sm text-warning-800 dark:text-warning-300">
                    There are unposted Journal Entries prior or included in this period.
                </p>
            </div>
        @endif

        <div class="mt-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Aged Payable - As of {{ $asOfDate->format('m/d/Y') }}
                </h2>
                <div class="flex gap-2">
                    <button 
                        type="button"
                        onclick="window.print()"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        PDF
                    </button>
                </div>
            </div>

            @if(empty($partners))
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    No receivables found for the selected criteria.
                </div>
            @else
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Aged Payable
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
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
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
                                <tbody x-data="{ expanded: false }">
                                    {{-- Partner Header --}}
                                    <tr class="bg-gray-50 dark:bg-gray-800 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
                                        @click="expanded = !expanded">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 transition-transform" :class="{ 'rotate-90': expanded }" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                </svg>
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $partner['partner_name'] }}</span>
                                            </div>
                                        </td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <span class="{{ $partner['at_date'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                            {{ $partner['at_date'] != 0 ? '$' . number_format(abs($partner['at_date']), 2) : '' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <span class="{{ $partner['period_1'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                            {{ $partner['period_1'] != 0 ? '$' . number_format(abs($partner['period_1']), 2) : '' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <span class="{{ $partner['period_2'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                            {{ $partner['period_2'] != 0 ? '$' . number_format(abs($partner['period_2']), 2) : '' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <span class="{{ $partner['period_3'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                            {{ $partner['period_3'] != 0 ? '$' . number_format(abs($partner['period_3']), 2) : '' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <span class="{{ $partner['period_4'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                            {{ $partner['period_4'] != 0 ? '$' . number_format(abs($partner['period_4']), 2) : '' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <span class="{{ $partner['older'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                            {{ $partner['older'] != 0 ? '$' . number_format(abs($partner['older']), 2) : '' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap font-semibold">
                                        <span class="{{ $partner['total'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                            ${{ number_format(abs($partner['total']), 2) }}
                                        </span>
                                    </td>
                                </tr>

                                {{-- Partner Lines --}}
                                @foreach($partner['lines'] as $line)
                                    <tr x-show="expanded" x-cloak class="bg-white dark:bg-gray-900">
                                        <td class="px-4 py-2 pl-12 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                            {{ $line['move_name'] }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                            {{ Carbon\Carbon::parse($line['invoice_date'])->format('m/d/Y') }}
                                        </td>
                                        <td class="px-4 py-2 text-right text-sm whitespace-nowrap">
                                            <span class="{{ $line['at_date'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                                                {{ $line['at_date'] != 0 ? '$' . number_format(abs($line['at_date']), 2) : '' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-right text-sm whitespace-nowrap">
                                            <span class="{{ $line['period_1'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                                                {{ $line['period_1'] != 0 ? '$' . number_format(abs($line['period_1']), 2) : '' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-right text-sm whitespace-nowrap">
                                            <span class="{{ $line['period_2'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                                                {{ $line['period_2'] != 0 ? '$' . number_format(abs($line['period_2']), 2) : '' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-right text-sm whitespace-nowrap">
                                            <span class="{{ $line['period_3'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                                                {{ $line['period_3'] != 0 ? '$' . number_format(abs($line['period_3']), 2) : '' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-right text-sm whitespace-nowrap">
                                            <span class="{{ $line['period_4'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                                                {{ $line['period_4'] != 0 ? '$' . number_format(abs($line['period_4']), 2) : '' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-right text-sm whitespace-nowrap">
                                            <span class="{{ $line['older'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                                                {{ $line['older'] != 0 ? '$' . number_format(abs($line['older']), 2) : '' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2"></td>
                                    </tr>
                                @endforeach

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
                            <tr class="bg-gray-100 dark:bg-gray-800 font-semibold border-t-2 border-gray-300 dark:border-gray-600">
                                <td class="px-4 py-3 text-gray-900 dark:text-white">Total Aged Payable</td>
                                <td class="px-4 py-3"></td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <span class="{{ $totals['at_date'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                        {{ $totals['at_date'] != 0 ? '$' . number_format(abs($totals['at_date']), 2) : '' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <span class="{{ $totals['period_1'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                        {{ $totals['period_1'] != 0 ? '$' . number_format(abs($totals['period_1']), 2) : '' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <span class="{{ $totals['period_2'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                        {{ $totals['period_2'] != 0 ? '$' . number_format(abs($totals['period_2']), 2) : '' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <span class="{{ $totals['period_3'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                        {{ $totals['period_3'] != 0 ? '$' . number_format(abs($totals['period_3']), 2) : '' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <span class="{{ $totals['period_4'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                        {{ $totals['period_4'] != 0 ? '$' . number_format(abs($totals['period_4']), 2) : '' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <span class="{{ $totals['older'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                        {{ $totals['older'] != 0 ? '$' . number_format(abs($totals['older']), 2) : '' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap text-gray-900 dark:text-white">
                                    ${{ number_format(abs($totals['total']), 2) }}
                                </td>
                            </tr>
                            </tbody>
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
