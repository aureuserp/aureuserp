<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filters --}}
        {{ $this->form }}

        {{-- Report Header --}}
        <x-filament::section>
            @php
                $data = $this->balanceSheetData;
            @endphp
            
            <x-slot name="heading">
                Balance Sheet - As of {{ \Carbon\Carbon::parse($data['date'])->format('M d, Y') }}
            </x-slot>
            
            {{-- Balance Sheet Table --}}
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-white/5!">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-white/5!">
                    <thead class="bg-gray-50/50 dark:bg-white/5">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Account
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Balance
                            </th>
                        </tr>
                    </thead>
                    
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5!">
                        @foreach($data['sections'] as $sectionIndex => $section)
                            {{-- Section Header --}}
                            <tr class="bg-gray-100/80 dark:bg-white/5">
                                <td colspan="2" class="px-4 py-3 text-base font-bold text-gray-900 dark:text-white">
                                    {{ $section['title'] }}
                                </td>
                            </tr>

                            {{-- Subsections --}}
                            @foreach($section['subsections'] as $subsection)
                                @php
                                    $hasAccounts = count($subsection['accounts']) > 0;
                                    $showSubsection = $hasAccounts || !isset($subsection['show_if_empty']) || $subsection['show_if_empty'];
                                @endphp

                                @if($showSubsection)
                                    {{-- Subsection Header --}}
                                    <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                        <td class="px-4 py-2 font-semibold text-gray-900 dark:text-white" style="padding-left: 2rem;">
                                            {{ $subsection['title'] }}
                                        </td>
                                        <td class="px-4 py-2 text-right"></td>
                                    </tr>

                                    {{-- Accounts --}}
                                    @if($hasAccounts)
                                        @foreach($subsection['accounts'] as $account)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5!">
                                                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400" style="padding-left: 4rem;">
                                                    {{ $account['code'] ? $account['code'] . ' - ' : '' }}{{ $account['name'] }}
                                                </td>
                                                <td class="px-4 py-2 text-right text-sm text-gray-900 dark:text-gray-100!">
                                                    {{ number_format($account['balance'], 2) }}
                                                </td>
                                            </tr>
                                        @endforeach

                                        {{-- Subsection Total --}}
                                        <tr class="border-t border-gray-200 font-semibold bg-gray-50/50 dark:border-gray-700 dark:bg-gray-900/50">
                                            <td class="px-4 py-2 text-gray-900 dark:text-white" style="padding-left: 2rem;">
                                                {{ $subsection['total_label'] }}
                                            </td>
                                            <td class="px-4 py-2 text-right text-gray-900 dark:text-white">
                                                {{ number_format($subsection['total'], 2) }}
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            @endforeach

                            {{-- Section Total --}}
                            <tr class="border-t-2 border-gray-300 font-bold bg-gray-100/80 dark:border-gray-600 dark:bg-white/5">
                                <td class="px-4 py-3 text-gray-900 dark:text-white">
                                    {{ $section['total_label'] }}
                                </td>
                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">
                                    {{ number_format($section['total'], 2) }}
                                </td>
                            </tr>
                        @endforeach

                        {{-- Grand Total --}}
                        <tr class="border-t-4 border-gray-400 bg-gray-200/80 text-base font-bold dark:border-white/5! dark:bg-gray-700/80">
                            <td class="px-4 py-3 text-gray-900 dark:text-white">
                                {{ $data['grand_total_label'] }}
                            </td>
                            <td class="px-4 py-3 text-right text-gray-900 dark:text-white">
                                {{ number_format($data['grand_total'], 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
