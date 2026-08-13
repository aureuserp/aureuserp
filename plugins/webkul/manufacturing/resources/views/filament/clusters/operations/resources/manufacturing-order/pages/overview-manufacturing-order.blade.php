<x-filament-panels::page>
    @php
        $record = $this->getRecord();
        $uomName = $this->getOverviewUomName();
        $componentRows = $this->getComponentRows();
        $workOrderRows = $this->getWorkOrderRows();
        $showActualUsage = $record->state === \Webkul\Manufacturing\Enums\ManufacturingOrderState::DONE;
        $showInventoryColumns = ! $showActualUsage;
    @endphp

    <div class="space-y-6">
        <x-filament::section>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-white/5">
                    <thead>
                        <tr class="text-left text-sm font-medium text-gray-500 dark:text-gray-400">
                            <th class="px-4 py-3">{{ __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.table.columns.product') }}</th>
                            <th class="px-4 py-3">{{ __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.table.columns.status') }}</th>
                            <th class="px-4 py-3">{{ __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.table.columns.quantity') }}</th>
                            @if ($showInventoryColumns)
                                <th class="px-4 py-3">{{ __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.table.columns.free-to-use-on-hand') }}</th>
                                <th class="px-4 py-3">{{ __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.table.columns.reserved') }}</th>
                                <th class="px-4 py-3">{{ __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.table.columns.receipt') }}</th>
                            @endif
                            <th class="px-4 py-3 text-right">{{ __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.table.columns.unit-cost') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.table.columns.mo-cost') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.table.columns.bom-cost') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.table.columns.real-cost') }}</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                        <tr>
                            <td class="px-4 py-4 font-medium text-primary-700 dark:text-primary-400">
                                {{ $this->getOverviewProductName() }}
                            </td>
                            <td class="px-4 py-4">
                                <x-filament::badge :color="$this->getOrderStatusColor()">
                                    {{ $this->getOrderStatusLabel() }}
                                </x-filament::badge>
                            </td>
                            <td class="px-4 py-4">
                                {{ number_format((float) $record->quantity, 2) }}
                                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">{{ $uomName }}</span>
                            </td>
                            @if ($showInventoryColumns)
                                <td class="px-4 py-4">
                                    {{ number_format($this->getProductAvailableQuantity(), 2) }} / {{ number_format($this->getProductOnHandQuantity(), 2) }}
                                </td>
                                <td class="px-4 py-4">0.00</td>
                                <td class="px-4 py-4 text-warning-700 dark:text-warning-400">
                                    {{ __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.receipt.expected', ['date' => $this->getReceiptDateLabel()]) }}
                                </td>
                            @endif
                            <td class="px-4 py-4 text-right">${{ number_format((float) ($record->product?->cost ?? 0), 2) }}</td>
                            <td class="px-4 py-4 text-right">${{ number_format($this->getTotalMoCost(), 2) }}</td>
                            <td class="px-4 py-4 text-right">${{ number_format($this->getTotalBomCost(), 2) }}</td>
                            <td class="px-4 py-4 text-right">${{ number_format($this->getTotalRealCost(), 2) }}</td>
                        </tr>

                        @foreach ($componentRows as $move)
                            <tr>
                                <td class="px-4 py-4 pl-10 text-primary-700 dark:text-primary-400">
                                    {{ $move->product?->name ?? '—' }}
                                </td>
                                <td class="px-4 py-4"></td>
                                <td class="px-4 py-4">
                                    @if ($showActualUsage)
                                        {{ number_format((float) ($move->quantity ?: 0), 2) }}
                                        <span class="mx-2 text-sm text-gray-500 dark:text-gray-400">/</span>
                                        {{ number_format((float) $move->product_uom_qty, 2) }}
                                    @else
                                        {{ number_format((float) $move->product_uom_qty, 2) }}
                                    @endif

                                    <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">{{ $move->uom?->name ?? $move->product?->uom?->name ?? '—' }}</span>
                                </td>
                                @if ($showInventoryColumns)
                                    <td class="px-4 py-4">
                                        {{ number_format((float) ($move->product?->free_qty ?? 0), 2) }} / {{ number_format((float) ($move->product?->available_qty ?? 0), 2) }}
                                    </td>
                                    <td class="px-4 py-4">{{ number_format($this->getReservedQuantity($move), 2) }}</td>
                                    <td class="px-4 py-4" @class([
                                        'text-success-700 dark:text-success-400' => $this->getComponentStatusColor($move) === 'success',
                                        'text-warning-700 dark:text-warning-400' => $this->getComponentStatusColor($move) === 'warning',
                                    ])>
                                        {{ $this->getComponentReceiptLabel($move) }}
                                    </td>
                                @endif
                                <td class="px-4 py-4 text-right">${{ number_format($this->getComponentUnitCost($move), 2) }}</td>
                                <td class="px-4 py-4 text-right">${{ number_format($this->getComponentTotalCost($move), 2) }}</td>
                                <td class="px-4 py-4 text-right">${{ number_format($this->getComponentTotalCost($move), 2) }}</td>
                                <td class="px-4 py-4 text-right">${{ number_format($this->getComponentRealCost($move), 2) }}</td>
                            </tr>
                        @endforeach

                        <tr>
                            <td class="px-4 py-4 font-medium text-gray-950 dark:text-white">
                                {{ __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.table.sections.operations') }}
                            </td>
                            <td class="px-4 py-4"></td>
                            <td class="px-4 py-4">
                                @if ($showActualUsage)
                                    {{ format_float_time((float) $workOrderRows->sum('duration'), 'minutes') }}
                                    <span class="mx-2 text-sm text-gray-500 dark:text-gray-400">/</span>
                                    {{ $this->getTotalOperationDurationLabel() }}
                                @else
                                    {{ $this->getTotalOperationDurationLabel() }}
                                @endif

                                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.table.rows.minutes') }}
                                </span>
                            </td>
                            @if ($showInventoryColumns)
                                <td class="px-4 py-4"></td>
                                <td class="px-4 py-4"></td>
                                <td class="px-4 py-4"></td>
                            @endif
                            <td class="px-4 py-4 text-right">${{ number_format($workOrderRows->sum(fn ($workOrder) => $this->getWorkOrderUnitCost($workOrder)), 2) }}</td>
                            <td class="px-4 py-4 text-right">${{ number_format($this->getTotalOperationCost(), 2) }}</td>
                            <td class="px-4 py-4 text-right">${{ number_format($this->getTotalOperationCost(), 2) }}</td>
                            <td class="px-4 py-4 text-right">${{ number_format($this->getTotalRealOperationCost(), 2) }}</td>
                        </tr>

                        @foreach ($workOrderRows as $workOrder)
                            <tr>
                                <td class="px-4 py-4 pl-10 text-primary-700 dark:text-primary-400">
                                    {{ $workOrder->name }}
                                </td>
                                <td class="px-4 py-4">
                                    @if ($workOrder->state)
                                        <x-filament::badge :color="$workOrder->state->getColor()">
                                            {{ $workOrder->state->getLabel() }}
                                        </x-filament::badge>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @if ($showActualUsage)
                                        {{ format_float_time((float) ($workOrder->duration ?: 0), 'minutes') }}
                                        <span class="mx-2 text-sm text-gray-500 dark:text-gray-400">/</span>
                                    @endif
                                    {{ format_float_time((float) $workOrder->expected_duration, 'minutes') }}
                                    <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">{{ __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.table.rows.minutes') }}</span>
                                </td>
                                @if ($showInventoryColumns)
                                    <td class="px-4 py-4"></td>
                                    <td class="px-4 py-4"></td>
                                    <td class="px-4 py-4"></td>
                                @endif
                                <td class="px-4 py-4 text-right">${{ number_format($this->getWorkOrderUnitCost($workOrder), 2) }}</td>
                                <td class="px-4 py-4 text-right">${{ number_format($this->getWorkOrderTotalCost($workOrder), 2) }}</td>
                                <td class="px-4 py-4 text-right">${{ number_format($this->getWorkOrderTotalCost($workOrder), 2) }}</td>
                                <td class="px-4 py-4 text-right">${{ number_format($this->getWorkOrderRealCost($workOrder), 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot class="border-t border-gray-200 dark:border-white/5">
                        <tr>
                            <td colspan="{{ $showInventoryColumns ? 7 : 4 }}" class="px-4 py-4 text-right font-semibold text-gray-950 dark:text-white">
                                {{ __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/overview-manufacturing-order.table.footer.unit-cost') }}
                            </td>
                            <td class="px-4 py-4 text-right font-semibold text-gray-950 dark:text-white">${{ number_format($this->getUnitCost(), 2) }}</td>
                            <td class="px-4 py-4 text-right font-semibold text-gray-950 dark:text-white">${{ number_format($this->getUnitCost(), 2) }}</td>
                            <td class="px-4 py-4 text-right font-semibold text-gray-950 dark:text-white">${{ number_format($this->getRealUnitCost(), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
