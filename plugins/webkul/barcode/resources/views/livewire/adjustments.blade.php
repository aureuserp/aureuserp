<main class="min-h-screen bg-gray-50 p-2" x-data="barcodeScanner('search', 'scan')">
    @unless (\Webkul\Barcode\Support\NativeApp::usesNativeNavigation())
        @include('barcode::components.header.web', [
            'title' => __('barcode::app.adjustments.title'),
            'breadcrumbs' => [
                ['label' => __('barcode::app.title'), 'href' => route('barcode.dashboard')],
                ['label' => __('barcode::app.adjustments.title')],
            ],
            'showBarcode' => $editingQuantity ? null : true,
            'showCancel' => $editingQuantity ? true : null,
            'cancelAction' => 'discardQuantityEdit',
        ])
    @endunless

    @if ($editingQuantity)
        @php
            $productImages = $editingQuantity->product?->images ?? [];
            $productImage = is_array($productImages) ? ($productImages[0] ?? null) : null;
            $productImageUrl = is_string($productImage) && $productImage !== '' ? (str_starts_with($productImage, 'http') || str_starts_with($productImage, '/') ? $productImage : asset('storage/'.$productImage)) : null;
            $currentCountedQuantity = $editingQuantity->inventory_quantity_set ? (float) $editingQuantity->counted_quantity : 0.0;
            $remainingToFullCount = max((float) $editingQuantity->quantity - $currentCountedQuantity, 0);
        @endphp

        <section class="mx-auto">
            <x-filament::section compact class="mb-3">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex min-w-0 flex-col gap-1">
                        <strong class="block text-xl leading-6 font-medium text-gray-950">{{ $editingQuantity->location?->full_name ?? $editingQuantity->location?->name }}</strong>
                        <span class="text-sm leading-5 text-gray-950">
                            {{ $editingQuantity->product?->name }}
                            @if ($editingQuantity->lot?->name)
                                {{ __('barcode::app.adjustments.lot-serial') }}: {{ $editingQuantity->lot->name }}
                            @endif
                        </span>
                    </div>

                    <div class="inline-flex h-[72px] w-[72px] shrink-0 items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-100">
                        @if ($productImageUrl)
                            <img src="{{ $productImageUrl }}" alt="{{ __('barcode::app.adjustments.edit-tooltip') }}" class="h-full w-full object-cover">
                        @else
                            <span class="text-lg font-extrabold text-gray-500">{{ mb_substr((string) $editingQuantity->product?->name, 0, 1) }}</span>
                        @endif
                    </div>
                </div>
            </x-filament::section>

            <form wire:submit="confirmQuantityEdit">
                <x-filament::section compact class="mb-3">
                    <x-slot name="heading">
                        {{ __('barcode::app.adjustments.editor-title') }}
                    </x-slot>

                    <x-slot name="description">
                        {{ __('barcode::app.adjustments.editor-subtitle') }}
                    </x-slot>

                    <div class="grid grid-cols-[minmax(0,1fr)_120px] gap-3">
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="number"
                                min="0"
                                step="0.01"
                                wire:model="editingCountedQuantity"
                            />
                        </x-filament::input.wrapper>
                        <div class="flex min-h-10 items-center rounded-md border border-gray-200 bg-gray-100 px-4 text-base text-gray-950">{{ $editingQuantity->product?->uom?->name }}</div>
                    </div>

                    <div class="mt-3 grid grid-cols-4 gap-2">
                        <x-filament::button color="gray" class="w-full justify-center" type="button" wire:click="$set('editingCountedQuantity', 0)">0</x-filament::button>
                        <x-filament::button color="gray" class="w-full justify-center" type="button" wire:click="$set('editingCountedQuantity', {{ max($currentCountedQuantity - 1, 0) }})">-1</x-filament::button>
                        <x-filament::button color="gray" class="w-full justify-center" type="button" wire:click="$set('editingCountedQuantity', {{ $currentCountedQuantity + 1 }})">+1</x-filament::button>
                        <x-filament::button color="success" class="w-full justify-center" type="button" wire:click="$set('editingCountedQuantity', {{ $currentCountedQuantity + $remainingToFullCount }})">
                            +{{ number_format($remainingToFullCount, 0) }}
                        </x-filament::button>
                    </div>

                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-xs">
                            <span class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('barcode::app.adjustments.location') }}</span>
                            <strong class="mt-1 block text-sm font-semibold text-gray-950">{{ $editingQuantity->location?->full_name ?? '—' }}</strong>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-xs">
                            <span class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('barcode::app.adjustments.lot-serial') }}</span>
                            <strong class="mt-1 block text-sm font-semibold text-gray-950">{{ $editingQuantity->lot?->name ?? '—' }}</strong>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-xs">
                            <span class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('barcode::app.adjustments.on-hand') }}</span>
                            <strong class="mt-1 block text-sm font-semibold text-gray-950">{{ number_format((float) $editingQuantity->quantity, 2) }} {{ $editingQuantity->product?->uom?->name }}</strong>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-xs">
                            <span class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('barcode::app.adjustments.counted') }}</span>
                            <strong class="mt-1 block text-sm font-semibold text-gray-950">{{ number_format($currentCountedQuantity, 2) }} {{ $editingQuantity->product?->uom?->name }}</strong>
                        </div>
                    </div>
                </x-filament::section>
            </form>
        </section>

        <footer class="fixed inset-x-0 bottom-0 z-20 grid grid-cols-2 gap-2 border-t border-gray-200 bg-white px-2 py-2 shadow-[0_-4px_16px_rgba(15,23,42,0.08)]" style="padding-bottom: calc(0.5rem + var(--inset-bottom, 0px))">
            <x-filament::button color="gray" class="w-full justify-center" wire:click="discardQuantityEdit">
                {{ __('barcode::app.operation.discard') }}
            </x-filament::button>
            <x-filament::button color="primary" class="w-full justify-center" wire:click="confirmQuantityEdit">
                {{ __('barcode::app.operation.confirm') }}
            </x-filament::button>
        </footer>
    @else
        <div id="barcode-reader" class="fixed inset-x-2 top-2 z-40 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg" x-show="active" x-cloak></div>

        <div class="mb-3" x-show="scannerError" x-cloak>
            <x-filament::callout icon="heroicon-o-exclamation-triangle" color="warning">
                <x-slot name="heading">
                    {{ __('barcode::app.operation.camera-unavailable') }}
                </x-slot>

                <x-slot name="description">
                    <span x-text="scannerError"></span>
                </x-slot>
            </x-filament::callout>
        </div>

        <form class="mb-3" wire:submit="scan">
            <x-filament::input.wrapper>
                <x-slot name="suffix">
                    <x-filament::icon-button
                        color="primary"
                        icon="heroicon-m-arrow-right"
                        :label="__('barcode::app.operation-search.open')"
                        type="submit"
                        size="sm"
                        class="h-10 w-10"
                    />
                </x-slot>

                <x-filament::input
                    type="search"
                    wire:model.live.debounce.250ms="search"
                    :placeholder="__('barcode::app.adjustments.search')"
                    autocomplete="off"
                />
            </x-filament::input.wrapper>
        </form>

        @if ($selectedLocation || $selectedProduct || $selectedLot)
            <x-filament::callout icon="heroicon-o-funnel" color="info" class="mb-3">
                <x-slot name="heading">
                    {{ __('barcode::app.adjustments.title') }}
                </x-slot>

                <x-slot name="description">
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-700">
                        @if ($selectedLocation)
                            <span>{{ __('barcode::app.adjustments.location') }}: <strong>{{ $selectedLocation->full_name ?? $selectedLocation->name }}</strong></span>
                        @endif
                        @if ($selectedProduct)
                            <span>{{ __('barcode::app.adjustments.product') }}: <strong>{{ $selectedProduct->name }}</strong></span>
                        @endif
                        @if ($selectedLot)
                            <span>{{ __('barcode::app.adjustments.lot-serial') }}: <strong>{{ $selectedLot->name }}</strong></span>
                        @endif

                        <button type="button" class="inline-flex items-center justify-center rounded border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-700" wire:click="clearFilters">
                            {{ __('barcode::app.adjustments.clear-filters') }}
                        </button>
                    </div>
                </x-slot>
            </x-filament::callout>
        @elseif ($notice)
            <x-filament::callout icon="heroicon-o-information-circle" :color="$noticeColor" class="mb-3">
                <x-slot name="heading">
                    {{ __('barcode::app.adjustments.title') }}
                </x-slot>

                <x-slot name="description">
                    {{ $notice }}
                </x-slot>
            </x-filament::callout>
        @endif

        <section class="grid gap-2 pb-24">
            <div class="text-sm font-semibold uppercase tracking-wide text-gray-950">{{ __('barcode::app.adjustments.title') }}</div>

            @forelse ($quantities as $quantity)
                @php
                    $productImages = $quantity->product?->images ?? [];
                    $productImage = is_array($productImages) ? ($productImages[0] ?? null) : null;
                    $productImageUrl = is_string($productImage) && $productImage !== '' ? (str_starts_with($productImage, 'http') || str_starts_with($productImage, '/') ? $productImage : asset('storage/'.$productImage)) : null;
                    $countedQuantity = $quantity->inventory_quantity_set ? (float) $quantity->counted_quantity : 0.0;
                    $onHandQuantity = (float) $quantity->quantity;
                    $countState = ! $quantity->inventory_quantity_set
                        ? ''
                        : ($countedQuantity === $onHandQuantity ? 'is-complete' : 'is-partial');
                @endphp

                <article
                    id="quantity-{{ $quantity->id }}"
                    @class([
                        'flex items-start justify-between gap-3 rounded-lg border shadow-xs',
                        'border-gray-200 bg-white' => $countState === '',
                        'border-[var(--success-500)] bg-[var(--success-50)]' => $countState === 'is-complete',
                        'border-[var(--warning-500)] bg-[var(--warning-50)]' => $countState === 'is-partial',
                    ])
                    wire:key="quantity-{{ $quantity->id }}"
                >
                    <div class="min-w-0 flex-1 px-4 py-4">
                        <div class="flex flex-col gap-1">
                            <strong class="block text-xl leading-6 font-medium text-gray-950">{{ $quantity->location?->full_name ?? $quantity->location?->name }}</strong>
                            <span class="text-sm leading-5 text-gray-950">{{ $quantity->product?->name }}</span>
                            @if ($quantity->lot?->name)
                                <span class="text-sm leading-5 text-gray-950">{{ __('barcode::app.adjustments.lot-serial') }}: {{ $quantity->lot->name }}</span>
                            @endif
                            <div class="mt-4 flex items-baseline gap-1.5">
                                <strong @class([
                                    'text-[32px] leading-none font-medium',
                                    'text-gray-950' => $countState === '',
                                    'text-[var(--warning-600)]' => $countState === 'is-partial',
                                    'text-[var(--success-600)]' => $countState === 'is-complete',
                                ])>{{ number_format($countedQuantity, 0) }} / {{ number_format($onHandQuantity, 0) }}</strong>
                                <span class="text-sm font-bold text-gray-950">{{ $quantity->product?->uom?->name }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-col items-end gap-7 px-4 py-3 text-right">
                        <div class="grid w-[92px] grid-cols-2 gap-2">
                            <div class="inline-flex h-[42px] w-[42px] items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-100">
                                @if ($productImageUrl)
                                    <img src="{{ $productImageUrl }}" alt="{{ __('barcode::app.adjustments.edit-tooltip') }}" class="h-full w-full object-cover">
                                @else
                                    <span class="text-lg font-extrabold text-gray-500">{{ mb_substr((string) $quantity->product?->name, 0, 1) }}</span>
                                @endif
                            </div>

                            <x-filament::button
                                color="gray"
                                outlined
                                icon="heroicon-m-pencil-square"
                                class="h-[42px] w-[42px] justify-center"
                                wire:click="editQuantity({{ $quantity->id }})"
                                tooltip="{{ __('barcode::app.adjustments.edit-tooltip') }}"
                            />
                        </div>

                        <div class="flex min-h-[42px] w-[92px] items-end justify-end gap-2">
                            @if (! $quantity->inventory_quantity_set || $countedQuantity <= 0)
                                <x-filament::button color="gray" outlined type="button" class="h-[42px] min-w-[42px] justify-center px-3" wire:click="quickCountQuantity({{ $quantity->id }})">+{{ number_format($onHandQuantity, 0) }}</x-filament::button>
                            @else
                                <x-filament::button
                                    color="success"
                                    outlined
                                    icon="heroicon-m-check"
                                    class="h-[42px] w-[42px] justify-center"
                                    wire:click="applyQuantityCount({{ $quantity->id }})"
                                    tooltip="{{ __('barcode::app.adjustments.apply') }}"
                                />
                                <x-filament::button
                                    color="danger"
                                    outlined
                                    icon="heroicon-m-x-mark"
                                    class="h-[42px] w-[42px] justify-center"
                                    wire:click="clearQuantityCount({{ $quantity->id }})"
                                    tooltip="{{ __('barcode::app.adjustments.clear') }}"
                                />
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="flex flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-gray-300 bg-white px-6 py-10 text-center text-gray-600">
                    <x-filament::icon icon="heroicon-o-inbox" class="h-8 w-8 text-gray-400" />
                    <div>{{ __('barcode::app.adjustments.empty') }}</div>
                </div>
            @endforelse
        </section>
    @endif
</main>
