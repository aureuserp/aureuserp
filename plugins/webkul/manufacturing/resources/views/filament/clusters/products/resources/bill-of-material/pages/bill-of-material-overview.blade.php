<x-filament-panels::page>
    @php
        $record = $this->getRecord();
        $product = $record->product;
        $productRows = $this->getProductRows();
        $operationRows = $this->getOperationRows();
        $byproductRows = $this->getByproductRows();
        $uomName = $record->uom?->name ?? $product?->uom?->name ?? '—';
        $selectedVariantLabel = $this->getSelectedVariantLabel();
    @endphp

    <div class="space-y-6">
        <x-filament::section>
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-4 lg:col-span-2">
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.title') }}
                    </p>

                    <h2 class="text-2xl font-semibold text-gray-950 dark:text-white">
                        {{ $product?->name ?? '—' }}
                    </h2>

                    @if ($selectedVariantLabel)
                        <div class="rounded-lg border border-dashed border-gray-200 px-4 py-3 text-sm dark:border-white/5">
                            <span class="text-gray-500 dark:text-gray-400">
                                {{ __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.filters.variant') }}
                            </span>

                            <span class="ml-2 font-medium text-gray-950 dark:text-white">
                                {{ $selectedVariantLabel }}
                            </span>
                        </div>
                    @endif
                </div>

                <div class="space-y-4">
                    <form wire:submit.prevent="$refresh" class="rounded-lg border border-gray-200 bg-gray-50/50 p-4 dark:border-white/5 dark:bg-white/5">
                        {{ $this->form }}
                    </form>
                </div>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <div class="rounded-lg border border-gray-200 bg-gray-50/50 p-4 text-right dark:border-white/5 dark:bg-white/5">
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.summary.free-to-use') }}
                        </p>

                        <p class="mt-2 text-3xl font-semibold text-gray-950 dark:text-white">
                            {{ number_format($this->getProductAvailableQuantity(), 2) }}
                        </p>

                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $uomName }}</p>
                </div>

                <div class="rounded-lg border border-gray-200 bg-gray-50/50 p-4 text-right dark:border-white/5 dark:bg-white/5">
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.summary.on-hand') }}
                        </p>

                        <p class="mt-2 text-3xl font-semibold text-gray-950 dark:text-white">
                            {{ number_format($this->getProductOnHandQuantity(), 2) }}
                        </p>

                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $uomName }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $this->getProductDateLabel() }}</p>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-white/5">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-white/5">
                    <thead class="bg-gray-50/50 dark:bg-white/5">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.table.columns.product') }}
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.table.columns.quantity') }}
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.table.columns.lead-time') }}
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.table.columns.route') }}
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.table.columns.bom-cost') }}
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.table.columns.product-cost') }}
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                        @foreach ($productRows as $row)
                            <tr class="{{ $row['is_parent'] ? 'bg-gray-50/50 dark:bg-white/5' : 'hover:bg-gray-50 dark:hover:bg-white/5' }}">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="{{ $row['is_parent'] ? 'font-medium text-gray-950 dark:text-white' : 'pl-5 text-gray-700 dark:text-gray-300' }}">
                                        {{ $row['label'] }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                    <span>{{ number_format($row['quantity'], 2) }}</span>
                                    <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">{{ $row['uom'] }}</span>
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                    {{ filled($row['lead_time']) ? $row['lead_time'] . ' ' . __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.table.rows.days') : '—' }}
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                    {{ $row['route'] }}
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-right font-medium text-gray-950 dark:text-white">
                                    ${{ number_format($row['bom_cost'], 2) }}
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-right font-medium text-gray-950 dark:text-white">
                                    ${{ number_format($row['product_cost'], 2) }}
                                </td>
                            </tr>
                        @endforeach

                        <tr class="bg-gray-50/50 dark:bg-white/5">
                            <td class="px-4 py-3 font-medium text-gray-950 dark:text-white">
                                {{ __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.table.sections.operations') }}
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                <span>{{ $this->getTotalOperationDurationLabel() }}</span>
                                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.table.rows.minutes') }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">—</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">—</td>

                            <td class="px-4 py-3 whitespace-nowrap text-right font-medium text-gray-950 dark:text-white">
                                ${{ number_format($this->getTotalOperationCost(), 2) }}
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap text-right text-gray-700 dark:text-gray-300">—</td>
                        </tr>

                        @foreach ($operationRows as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="px-4 py-3 whitespace-nowrap pl-9 text-gray-700 dark:text-gray-300">
                                    {{ $row['label'] }}
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                    <span>{{ $row['duration_label'] }}</span>
                                    <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">{{ $row['uom'] }}</span>
                                </td>

                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">—</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">—</td>

                                <td class="px-4 py-3 whitespace-nowrap text-right font-medium text-gray-950 dark:text-white">
                                    ${{ number_format($row['cost'], 2) }}
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-right text-gray-700 dark:text-gray-300">—</td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot class="bg-gray-100/70 dark:bg-white/5">
                        <tr class="border-t border-gray-300 dark:border-white/5">
                            <td colspan="4" class="px-4 py-3 font-semibold text-gray-950 dark:text-white">
                                {{ __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.table.footer.unit-cost') }}
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap text-right font-semibold text-gray-950 dark:text-white">
                                ${{ number_format($this->getDisplayedUnitBomCost(), 2) }}
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap text-right font-semibold text-gray-950 dark:text-white">
                                ${{ number_format((float) ($product?->cost ?? 0), 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-filament::section>

        @if ($byproductRows->isNotEmpty())
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.by-products.title') }}
                </x-slot>

                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-white/5">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-white/5">
                        <thead class="bg-gray-50/50 dark:bg-white/5">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.by-products.columns.product') }}
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.by-products.columns.quantity') }}
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.by-products.columns.uom') }}
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                            @foreach ($byproductRows as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-950 dark:text-white">{{ $row['label'] }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ number_format($row['quantity'], 2) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $row['uom'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
