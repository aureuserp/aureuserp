<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filters --}}
        {{ $this->form }}

        {{-- Report Header --}}
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            @php
                $data = $this->balanceSheetData;
            @endphp
            
            @if(!empty($data))
                {{-- Balance Sheet Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b-2 border-gray-300 dark:border-gray-600">
                                <th class="px-4 py-2 text-left"></th>
                                <th class="px-4 py-2 text-right font-semibold">Balance</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            @foreach($data['sections'] as $sectionIndex => $section)
                                {{-- Section Header --}}
                                <tr class="{{ $sectionIndex > 0 ? 'border-t-4 border-gray-400 dark:border-gray-500' : '' }} bg-gray-100 dark:bg-gray-700">
                                    <td colspan="2" class="px-4 py-2 {{ $sectionIndex > 0 ? 'pt-4' : '' }} text-base font-bold">{{ $section['title'] }}</td>
                                </tr>

                                {{-- Subsections --}}
                                @foreach($section['subsections'] as $subsection)
                                    @php
                                        $hasAccounts = count($subsection['accounts']) > 0;
                                        $showSubsection = $hasAccounts || !isset($subsection['show_if_empty']) || $subsection['show_if_empty'];
                                    @endphp

                                    @if($showSubsection)
                                        {{-- Subsection Header --}}
                                        <tr class="bg-gray-50 dark:bg-gray-800">
                                            <td class="px-4 py-2 font-semibold" style="padding-left: 2rem;">{{ $subsection['title'] }}</td>
                                            <td class="px-4 py-1 text-right">0.00</td>
                                        </tr>

                                        {{-- Accounts --}}
                                        @if($hasAccounts)
                                            @foreach($subsection['accounts'] as $account)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                    <td class="px-4 py-1 text-gray-600 dark:text-gray-300" style="padding-left: 4rem;">
                                                        {{ $account['code'] ? $account['code'] . ' - ' : '' }}{{ $account['name'] }}
                                                    </td>
                                                    <td class="px-4 py-1 text-right">
                                                        {{ number_format($account['balance'], 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach

                                            {{-- Subsection Total --}}
                                            <tr class="border-t border-gray-200 font-semibold dark:border-gray-600">
                                                <td class="px-4 py-2" style="padding-left: 2rem;">{{ $subsection['total_label'] }}</td>
                                                <td class="px-4 py-2 text-right">{{ number_format($subsection['total'], 2) }}</td>
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach

                                {{-- Section Total --}}
                                <tr class="border-t-2 border-gray-300 font-bold dark:border-gray-600">
                                    <td class="px-4 py-2">{{ $section['total_label'] }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($section['total'], 2) }}</td>
                                </tr>
                            @endforeach

                            {{-- Grand Total --}}
                            <tr class="border-t-4 border-gray-400 bg-gray-200 text-base font-bold dark:border-gray-500 dark:bg-gray-600">
                                <td class="px-4 py-3">{{ $data['grand_total_label'] }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($data['grand_total'], 2) }}</td>
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
