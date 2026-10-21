<x-filament-panels::page>
    @php($report = $this->getReport())

    <x-filament::section>
        <x-slot name="heading">
            {{ __('point-of-sale::filament/admin/clusters/reporting/pages/order-report.filters.heading') }}
        </x-slot>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <x-filament::input.wrapper>
                <x-filament::input type="date" wire:model.live="startDate" />
            </x-filament::input.wrapper>

            <x-filament::input.wrapper>
                <x-filament::input type="date" wire:model.live="endDate" />
            </x-filament::input.wrapper>

            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.live="configId">
                    <option value="">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/order-report.filters.all-terminals') }}</option>

                    @foreach ($this->getTerminals() as $terminal)
                        <option value="{{ $terminal->id }}">{{ $terminal->name }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>

            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.live="groupBy">
                    <option value="day">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/order-report.filters.day') }}</option>
                    <option value="week">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/order-report.filters.week') }}</option>
                    <option value="month">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/order-report.filters.month') }}</option>
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>
    </x-filament::section>

    <x-filament::section>
        <x-slot name="heading">
            {{ __('point-of-sale::filament/admin/clusters/reporting/pages/order-report.table.heading') }}
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-start text-xs uppercase tracking-wide text-gray-500 dark:border-gray-700 dark:text-gray-400">
                        <th class="p-2">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/order-report.table.columns.period') }}</th>
                        <th class="p-2 text-end">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/order-report.table.columns.orders') }}</th>
                        <th class="p-2 text-end">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/order-report.table.columns.untaxed') }}</th>
                        <th class="p-2 text-end">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/order-report.table.columns.taxes') }}</th>
                        <th class="p-2 text-end">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/order-report.table.columns.total') }}</th>
                        <th class="p-2 text-end">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/order-report.table.columns.margin') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($report['rows'] as $row)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="p-2 text-gray-950 dark:text-white">{{ $row->bucket }}</td>
                            <td class="p-2 text-end tabular-nums text-gray-950 dark:text-white">{{ $row->order_count }}</td>
                            <td class="p-2 text-end tabular-nums text-gray-950 dark:text-white">{{ number_format((float) $row->untaxed, 2) }}</td>
                            <td class="p-2 text-end tabular-nums text-gray-950 dark:text-white">{{ number_format((float) $row->taxes, 2) }}</td>
                            <td class="p-2 text-end font-semibold tabular-nums text-gray-950 dark:text-white">{{ number_format((float) $row->total, 2) }}</td>
                            <td class="p-2 text-end tabular-nums text-gray-950 dark:text-white">{{ number_format((float) $row->margin, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500 dark:text-gray-400">
                                {{ __('point-of-sale::filament/admin/clusters/reporting/pages/order-report.table.empty') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot>
                    <tr class="border-t border-gray-200 font-semibold dark:border-gray-700">
                        <td class="p-2 text-gray-950 dark:text-white">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/order-report.table.total') }}</td>
                        <td class="p-2 text-end tabular-nums text-gray-950 dark:text-white">{{ $report['totals']['order_count'] }}</td>
                        <td class="p-2 text-end tabular-nums text-gray-950 dark:text-white">{{ number_format($report['totals']['untaxed'], 2) }}</td>
                        <td class="p-2 text-end tabular-nums text-gray-950 dark:text-white">{{ number_format($report['totals']['taxes'], 2) }}</td>
                        <td class="p-2 text-end tabular-nums text-gray-950 dark:text-white">{{ number_format($report['totals']['total'], 2) }}</td>
                        <td class="p-2 text-end tabular-nums text-gray-950 dark:text-white">{{ number_format($report['totals']['margin'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <x-slot name="footer">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('point-of-sale::filament/admin/clusters/reporting/pages/order-report.average', ['amount' => number_format($report['average'], 2)]) }}
            </p>
        </x-slot>
    </x-filament::section>
</x-filament-panels::page>
