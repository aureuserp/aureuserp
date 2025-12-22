<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filters --}}
        {{ $this->form }}

        {{-- Report Header --}}
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            @php
                $data = $this->profitLossData;
            @endphp
            
            @if(!empty($data))
                {{-- Profit & Loss Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b-2 border-gray-300 dark:border-gray-600">
                                <th class="px-4 py-2 text-left"></th>
                                <th class="px-4 py-2 text-right font-semibold">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['sections'] as $sectionIndex => $section)
                                {{-- Section Header --}}
                                <tr class="{{ $sectionIndex > 0 ? 'border-t-4 border-gray-400 dark:border-gray-500' : '' }} bg-gray-100 dark:bg-gray-700">
                                    <td colspan="2" class="px-4 py-2 {{ $sectionIndex > 0 ? 'pt-4' : '' }} text-base font-bold">{{ $section['title'] }}</td>
                                </tr>

                                {{-- Accounts --}}
                                @if(count($section['accounts']) > 0)
                                    @foreach($section['accounts'] as $account)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-4 py-1 text-gray-600 dark:text-gray-300" style="padding-left: 2rem;">
                                                {{ $account['code'] ? $account['code'] . ' - ' : '' }}{{ $account['name'] }}
                                            </td>
                                            <td class="px-4 py-1 text-right {{ isset($section['is_expense']) && $section['is_expense'] ? 'text-red-600 dark:text-red-400' : '' }}">
                                                {{ number_format($account['balance'], 2) }}
                                            </td>
                                        </tr>
                                    @endforeach

                                    {{-- Section Total --}}
                                    <tr class="border-t border-gray-200 font-semibold dark:border-gray-600">
                                        <td class="px-4 py-2">{{ $section['total_label'] }}</td>
                                        <td class="px-4 py-2 text-right {{ isset($section['is_expense']) && $section['is_expense'] ? 'text-red-600 dark:text-red-400' : '' }}">
                                            {{ number_format($section['total'], 2) }}
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="2" class="px-4 py-2 italic text-gray-500 dark:text-gray-400" style="padding-left: 2rem;">
                                            {{ $section['empty_message'] }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach

                            {{-- NET INCOME/LOSS --}}
                            <tr class="border-t-2 border-gray-300 font-bold dark:border-gray-600">
                                <td class="px-4 py-2">{{ $data['is_profit'] ? 'Net Profit' : 'Net Loss' }}</td>
                                <td class="text-right py-2 px-4 {{ $data['is_profit'] ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ number_format(abs($data['net_income']), 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-12 text-center">
                    <div class="mb-2 text-gray-400 dark:text-gray-600">
                        <x-filament::icon
                            icon="heroicon-o-document-text"
                            class="mx-auto h-16 w-16"
                        />
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No data available for the selected period</p>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
