<x-filament-panels::page>
    @php($report = $this->getReport())

    <x-filament::section>
        <x-slot name="heading">
            {{ __('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.filters.heading') }}
        </x-slot>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-filament::input.wrapper>
                <x-filament::input type="date" wire:model.live="startDate" />
            </x-filament::input.wrapper>

            <x-filament::input.wrapper>
                <x-filament::input type="date" wire:model.live="endDate" />
            </x-filament::input.wrapper>

            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.live="configId">
                    <option value="">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.filters.all-terminals') }}</option>

                    @foreach ($this->getTerminals() as $terminal)
                        <option value="{{ $terminal->id }}">{{ $terminal->name }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>
    </x-filament::section>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <x-filament::section>
            <x-slot name="heading">
                {{ __('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.summary.heading') }}
            </x-slot>

            <dl class="flex flex-col gap-2 text-sm">
                <div class="flex justify-between gap-3">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.summary.orders') }}</dt>
                    <dd class="tabular-nums text-gray-950 dark:text-white">{{ $report['orders']['count'] }}</dd>
                </div>

                <div class="flex justify-between gap-3">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.summary.untaxed') }}</dt>
                    <dd class="tabular-nums text-gray-950 dark:text-white">{{ number_format($report['orders']['untaxed'], 2) }}</dd>
                </div>

                <div class="flex justify-between gap-3">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.summary.taxes') }}</dt>
                    <dd class="tabular-nums text-gray-950 dark:text-white">{{ number_format($report['orders']['taxes'], 2) }}</dd>
                </div>

                <div class="flex justify-between gap-3 font-semibold">
                    <dt class="text-gray-950 dark:text-white">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.summary.total') }}</dt>
                    <dd class="tabular-nums text-gray-950 dark:text-white">{{ number_format($report['orders']['total'], 2) }}</dd>
                </div>
            </dl>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                {{ __('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.payments.heading') }}
            </x-slot>

            <div class="flex flex-col gap-2 text-sm">
                @forelse ($report['payments'] as $payment)
                    <div class="flex justify-between gap-3">
                        <span class="text-gray-950 dark:text-white">{{ $payment->paymentMethod?->name }}</span>
                        <span class="tabular-nums text-gray-950 dark:text-white">{{ number_format((float) $payment->amount, 2) }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.payments.empty') }}</p>
                @endforelse
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                {{ __('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.taxes.heading') }}
            </x-slot>

            <div class="flex flex-col gap-2 text-sm">
                @forelse ($report['taxes'] as $tax)
                    <div class="flex justify-between gap-3">
                        <span class="text-gray-950 dark:text-white">{{ $tax->name }}</span>
                        <span class="tabular-nums text-gray-950 dark:text-white">{{ number_format((float) $tax->amount, 2) }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.taxes.empty') }}</p>
                @endforelse
            </div>
        </x-filament::section>
    </div>

    <x-filament::section>
        <x-slot name="heading">
            {{ __('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.products.heading') }}
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-start text-xs uppercase tracking-wide text-gray-500 dark:border-gray-700 dark:text-gray-400">
                        <th class="p-2">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.products.columns.product') }}</th>
                        <th class="p-2 text-end">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.products.columns.quantity') }}</th>
                        <th class="p-2 text-end">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.products.columns.untaxed') }}</th>
                        <th class="p-2 text-end">{{ __('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.products.columns.total') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($report['products'] as $line)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="p-2 text-gray-950 dark:text-white">{{ $line->product?->name }}</td>
                            <td class="p-2 text-end tabular-nums text-gray-950 dark:text-white">{{ number_format((float) $line->quantity, 2) }}</td>
                            <td class="p-2 text-end tabular-nums text-gray-950 dark:text-white">{{ number_format((float) $line->untaxed, 2) }}</td>
                            <td class="p-2 text-end tabular-nums text-gray-950 dark:text-white">{{ number_format((float) $line->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500 dark:text-gray-400">
                                {{ __('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.products.empty') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
