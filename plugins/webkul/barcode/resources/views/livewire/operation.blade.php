@php
    $editingMoveLine = $editingMoveLineId ? $operation->moveLines->firstWhere('id', $editingMoveLineId) : null;
    $allMoveLinesCounted = $moveLines->isNotEmpty()
        && $moveLines->every(fn ($moveLine) => (float) ($countedMoveLineQuantities[$moveLine->id] ?? 0) >= (float) $moveLine->qty);
    $hasAnyCountedMoveLine = $moveLines->contains(fn ($moveLine) => (float) ($countedMoveLineQuantities[$moveLine->id] ?? 0) > 0);
@endphp

<main @class([
    'min-h-screen bg-gray-50 p-2',
]) x-data="barcodeScanner('barcode', 'scan')">
    @unless (\Webkul\Barcode\Support\NativeApp::usesNativeNavigation())
        @include('barcode::components.header.web', [
            'title' => $operation->name,
            'subtitle' => $operation->partner?->name ?? $operation->origin,
            'breadcrumbs' => [
                ['label' => __('barcode::app.title'), 'href' => route('barcode.dashboard')],
                ['label' => $operationType->name, 'href' => route('barcode.transfers', $operationType)],
            ],
            'showCancel' => $editingMoveLine ? true : null,
            'showBarcode' => $editingMoveLine ? null : true,
        ])
    @endunless

    @if ($editingMoveLine)
        @php
            $productImages = $editingMoveLine->product?->images ?? [];
            $productImage = is_array($productImages) ? ($productImages[0] ?? null) : null;
            $productImageUrl = is_string($productImage) && $productImage !== '' ? (str_starts_with($productImage, 'http') || str_starts_with($productImage, '/') ? $productImage : asset('storage/'.$productImage)) : null;
            $tracking = $editingMoveLine->product?->tracking;
        @endphp

        <section class="mx-auto">
            <x-filament::section compact class="mb-3">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex min-w-0 flex-col gap-1">
                        <strong class="block text-xl leading-6 font-medium text-gray-950">⌁ {{ $operation->name }}</strong>

                        <span class="text-sm leading-5 text-gray-950">
                            {{ $editingMoveLine->product?->name }}
                            @if ($editingMoveLine->product?->reference)
                                [{{ $editingMoveLine->product->reference }}]
                            @endif
                            {{ __('barcode::app.operation.source') }}:
                        </span>

                        <span class="text-sm leading-5 text-gray-950">{{ $editingMoveLine->sourceLocation?->full_name ?? $editingMoveLine->sourceLocation?->name }}</span>
                    </div>

                    <div class="inline-flex h-[72px] w-[72px] shrink-0 items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-100">
                        @if ($productImageUrl)
                            <img src="{{ $productImageUrl }}" alt="{{ __('barcode::app.operation.image-alt') }}" class="h-full w-full object-cover">
                        @else
                            <span class="text-lg font-extrabold text-gray-500">{{ mb_substr((string) $editingMoveLine->product?->name, 0, 1) }}</span>
                        @endif
                    </div>
                </div>
            </x-filament::section>

            <form wire:submit="confirmMoveLineEdit">
                <x-filament::section compact class="mb-3">
                    <x-slot name="heading">
                        {{ __('barcode::app.operation.details-title') }}
                    </x-slot>

                    <div class="grid grid-cols-[minmax(0,1fr)_120px] gap-3">
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="number"
                                min="0"
                                max="{{ (float) $editingMoveLine->qty }}"
                                step="0.01"
                                wire:model="countedMoveLineQuantities.{{ $editingMoveLine->id }}"
                            />
                        </x-filament::input.wrapper>
                        <div class="flex min-h-10 items-center rounded-md border border-gray-200 bg-gray-100 px-4 text-base text-gray-950">{{ $editingMoveLine->uom?->name }}</div>
                    </div>

                    <div class="mt-3 grid grid-cols-4 gap-2">
                        <x-filament::button color="gray" class="w-full justify-center" type="button" wire:click="setMoveLineQuantity({{ $editingMoveLine->id }}, 0)">0</x-filament::button>
                        <x-filament::button color="gray" class="w-full justify-center" type="button" wire:click="adjustMoveLineQuantity({{ $editingMoveLine->id }}, -1)">-1</x-filament::button>
                        <x-filament::button color="gray" class="w-full justify-center" type="button" wire:click="adjustMoveLineQuantity({{ $editingMoveLine->id }}, 1)">+1</x-filament::button>
                        <x-filament::button
                            color="success"
                            class="w-full justify-center"
                            type="button"
                            wire:click="adjustMoveLineQuantity({{ $editingMoveLine->id }}, {{ max((float) $editingMoveLine->qty - (float) ($countedMoveLineQuantities[$editingMoveLine->id] ?? 0), 0) }})"
                        >
                            +{{ number_format(max((float) $editingMoveLine->qty - (float) ($countedMoveLineQuantities[$editingMoveLine->id] ?? 0), 0), 0) }}
                        </x-filament::button>
                    </div>

                    <x-filament::fieldset :contained="false" class="mt-4">
                        <x-slot name="label">
                            {{ __('barcode::app.operation.settings-title') }}
                        </x-slot>

                        <div class="grid gap-4">
                            @if ($editingMoveLine->sourceLocation?->type === \Webkul\Inventory\Enums\LocationType::INTERNAL)
                                <label class="flex flex-col gap-1.5 text-sm font-medium text-gray-700">
                                    <span>{{ __('barcode::app.operation.pick-from') }}</span>
                                    <x-filament::input.wrapper>
                                        <x-filament::input.select wire:model.live="editingMoveLineQuantityId">
                                            @foreach ($editingMoveLineQuantityOptions as $quantityId => $quantityLabel)
                                                <option value="{{ $quantityId }}">{{ $quantityLabel }}</option>
                                            @endforeach
                                        </x-filament::input.select>
                                    </x-filament::input.wrapper>
                                </label>
                            @endif

                            @if ($tracking && $tracking !== \Webkul\Inventory\Enums\ProductTracking::QTY)
                                <label class="flex flex-col gap-1.5 text-sm font-medium text-gray-700">
                                    <span>{{ __('barcode::app.operation.lot-serial') }}</span>
                                    <x-filament::input.wrapper>
                                        <x-filament::input type="text" wire:model="editingMoveLineLotName" />
                                    </x-filament::input.wrapper>
                                </label>
                            @endif

                            <label class="flex flex-col gap-1.5 text-sm font-medium text-gray-700">
                                <span>{{ __('barcode::app.operation.destination-location') }}</span>
                                <x-filament::input.wrapper>
                                    <x-filament::input.select wire:model.live="editingMoveLineDestinationLocationId">
                                        @foreach ($editingMoveLineDestinationLocationOptions as $locationId => $locationLabel)
                                            <option value="{{ $locationId }}">{{ $locationLabel }}</option>
                                        @endforeach
                                    </x-filament::input.select>
                                </x-filament::input.wrapper>
                            </label>

                            @if ($editingMoveLineResultPackageOptions !== [])
                                <label class="flex flex-col gap-1.5 text-sm font-medium text-gray-700">
                                    <span>{{ __('barcode::app.operation.destination-package') }}</span>
                                    <x-filament::input.wrapper>
                                        <x-filament::input.select wire:model="editingMoveLineResultPackageId">
                                            <option value="">{{ __('barcode::app.operation.select-package') }}</option>
                                            @foreach ($editingMoveLineResultPackageOptions as $packageId => $packageLabel)
                                                <option value="{{ $packageId }}">{{ $packageLabel }}</option>
                                            @endforeach
                                        </x-filament::input.select>
                                    </x-filament::input.wrapper>
                                </label>
                            @endif
                        </div>
                    </x-filament::fieldset>
                </x-filament::section>
            </form>

            @if ($editingMoveLine->sourceLocation?->type === \Webkul\Inventory\Enums\LocationType::INTERNAL)
                <x-filament::section compact class="mb-3">
                    <x-slot name="heading">
                        {{ __('barcode::app.operation.stock-title') }}
                    </x-slot>

                    <x-slot name="description">
                        {{ __('barcode::app.operation.stock-subtitle') }}
                    </x-slot>

                    <div class="grid gap-2">
                        @forelse ($moveLineSourceLocationOptions as $option)
                            <button
                                type="button"
                                @class([
                                    'rounded-lg border px-4 py-3 text-left shadow-xs',
                                    'border-[var(--success-500)] bg-[var(--success-50)]' => (string) $editingMoveLineQuantityId === (string) $option['quantity_id'],
                                    'border-gray-200 bg-white' => (string) $editingMoveLineQuantityId !== (string) $option['quantity_id'],
                                ])
                                wire:click="selectEditingMoveLineSourceQuantity({{ $option['quantity_id'] }})"
                            >
                                <strong class="block text-sm font-semibold text-gray-950">{{ $option['location'] }}</strong>
                                @if ($option['lot'] || $option['package'])
                                    <span class="mt-1 block text-sm text-gray-700">{{ collect([$option['lot'], $option['package']])->filter()->implode(' - ') }}</span>
                                @endif
                                <span class="mt-1 block text-sm text-gray-700">{{ __('barcode::app.operation.available') }}: {{ number_format((float) $option['available'], 2) }} / {{ number_format((float) $option['quantity'], 2) }} {{ $option['uom'] }}</span>
                            </button>
                        @empty
                            <div class="flex items-center justify-center rounded-lg border border-dashed border-gray-300 bg-white px-4 py-6 text-sm text-gray-600">{{ __('barcode::app.operation.no-stock-locations') }}</div>
                        @endforelse
                    </div>
                </x-filament::section>
            @endif

            <div class="h-12" aria-hidden="true"></div>
        </section>

        <footer class="fixed inset-x-0 bottom-0 z-20 grid grid-cols-2 gap-2 border-t border-gray-200 bg-white px-2 py-2 shadow-[0_-4px_16px_rgba(15,23,42,0.08)]" style="padding-bottom: calc(0.5rem + var(--inset-bottom, 0px))">
            <x-filament::button color="gray" class="w-full justify-center" wire:click="discardMoveLineEdit">
                {{ __('barcode::app.operation.discard') }}
            </x-filament::button>
            <x-filament::button color="primary" class="w-full justify-center" wire:click="confirmMoveLineEdit">
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
                        :label="__('barcode::app.operation.submit-scan')"
                        type="submit"
                        size="sm"
                        class="h-10 w-10"
                    />
                </x-slot>

                <x-filament::input
                    type="search"
                    wire:model="barcode"
                    :placeholder="__('barcode::app.operation.manual-scan')"
                    autocomplete="off"
                />
            </x-filament::input.wrapper>
        </form>

        @if ($notice)
            <x-filament::callout icon="heroicon-o-information-circle" color="info" class="mb-3">
                <x-slot name="heading">
                    {{ __('barcode::app.title') }}
                </x-slot>

                <x-slot name="description">
                    {{ $notice }}
                </x-slot>
            </x-filament::callout>
        @endif

        <section class="grid gap-2 pb-24">
            <div class="text-sm font-semibold uppercase tracking-wide text-gray-950">{{ __('barcode::app.operation.moves') }}</div>

            @forelse ($moveLines as $moveLine)
                @php
                    $productImages = $moveLine->product?->images ?? [];
                    $productImage = is_array($productImages) ? ($productImages[0] ?? null) : null;
                    $productImageUrl = is_string($productImage) && $productImage !== '' ? (str_starts_with($productImage, 'http') || str_starts_with($productImage, '/') ? $productImage : asset('storage/'.$productImage)) : null;
                    $countedQuantity = (float) ($countedMoveLineQuantities[$moveLine->id] ?? 0);
                    $demandQuantity = (float) $moveLine->qty;
                    $countState = $countedQuantity >= $demandQuantity && $demandQuantity > 0 ? 'is-complete' : ($countedQuantity > 0 ? 'is-partial' : '');
                @endphp

                <article
                    id="line-{{ $moveLine->id }}"
                    @class([
                        'flex items-start justify-between gap-3 rounded-lg border shadow-xs',
                        'border-[var(--success-500)] bg-[var(--success-50)]' => $countState === 'is-complete',
                        'border-[var(--warning-500)] bg-[var(--warning-50)]' => $countState === 'is-partial',
                        'border-gray-200 bg-white' => $countState === '',
                    ])
                    wire:key="line-{{ $moveLine->id }}"
                >
                    <div class="min-w-0 flex-1 px-4 py-4">
                        <div class="flex flex-col gap-1">
                            <strong class="block text-xl leading-6 font-medium text-gray-950">{{ $moveLine->product?->reference ?? $moveLine->reference }}</strong>
                            <span class="text-sm leading-5 text-gray-950">{{ $moveLine->product?->name }}</span>
                            <span class="text-sm leading-5 text-gray-950">{{ __('barcode::app.operation.source') }}: {{ $moveLine->sourceLocation?->full_name ?? $moveLine->sourceLocation?->name }}</span>
                            @if ($moveLine->product?->barcode)
                                <span class="text-sm leading-5 text-gray-950">[{{ $moveLine->product->barcode }}]</span>
                            @endif
                            <div class="mt-4 flex items-baseline gap-1.5">
                                <strong @class([
                                    'text-[32px] leading-none font-medium',
                                    'text-gray-950' => $countState === '',
                                    'text-[var(--warning-600)]' => $countState === 'is-partial',
                                    'text-[var(--success-600)]' => $countState === 'is-complete',
                                ])>{{ number_format($countedQuantity, 0) }} / {{ number_format($demandQuantity, 0) }}</strong>
                                <span class="text-sm font-bold text-gray-950">{{ $moveLine->uom?->name }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-col items-end gap-7 px-4 py-3 text-right">
                        <div class="grid w-[92px] grid-cols-2 gap-2">
                            <div class="inline-flex h-[42px] w-[42px] items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-100">
                                @if ($productImageUrl)
                                    <img src="{{ $productImageUrl }}" alt="{{ __('barcode::app.operation.image-alt') }}" class="h-full w-full object-cover">
                                @else
                                    <span class="text-lg font-extrabold text-gray-500">{{ mb_substr((string) $moveLine->product?->name, 0, 1) }}</span>
                                @endif
                            </div>
                            <x-filament::button
                                color="gray"
                                outlined
                                icon="heroicon-m-pencil-square"
                                class="h-[42px] w-[42px] justify-center"
                                wire:click="editMoveLine({{ $moveLine->id }})"
                                tooltip="{{ __('barcode::app.operation.edit-tooltip') }}"
                            />
                        </div>

                        <div class="flex min-h-[42px] items-end justify-end gap-1.5">
                            @if ($countedQuantity <= 0)
                                <x-filament::button color="gray" outlined type="button" class="h-[42px] min-w-[42px] justify-center px-3" wire:click="setMoveLineQuantity({{ $moveLine->id }}, {{ $demandQuantity }})">+{{ number_format($demandQuantity, 0) }}</x-filament::button>
                            @elseif ($countedQuantity >= $demandQuantity)
                                <x-filament::button color="gray" outlined type="button" class="h-[42px] min-w-[42px] justify-center px-3" wire:click="adjustMoveLineQuantity({{ $moveLine->id }}, -1)">-1</x-filament::button>
                            @else
                                <x-filament::button color="gray" outlined type="button" class="h-[42px] min-w-[42px] justify-center px-3" wire:click="adjustMoveLineQuantity({{ $moveLine->id }}, 1)">+1</x-filament::button>
                                <x-filament::button color="gray" outlined type="button" class="h-[42px] min-w-[42px] justify-center px-3" wire:click="adjustMoveLineQuantity({{ $moveLine->id }}, -1)">-1</x-filament::button>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="flex flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-gray-300 bg-white px-6 py-10 text-center text-gray-600">
                    <x-filament::icon icon="heroicon-o-inbox" class="h-8 w-8 text-gray-400" />
                    <div>{{ __('barcode::app.operation.empty-moves') }}</div>
                </div>
            @endforelse
        </section>

        <footer class="fixed inset-x-0 bottom-0 z-20 grid auto-cols-fr grid-flow-col gap-2 border-t border-gray-200 bg-white px-2 py-2 shadow-[0_-4px_16px_rgba(15,23,42,0.08)]" style="padding-bottom: calc(0.5rem + var(--inset-bottom, 0px))">
            @foreach ($actions as $action)
                @if ($action['key'] === 'validate' || $action['key'] === 'done')
                    <x-filament::button
                        :color="$allMoveLinesCounted ? 'primary' : 'gray'"
                        class="w-full justify-center"
                        x-on:click="requestValidate('{{ addslashes($action['label']) }}', {{ Js::from($backorderMoveLines) }}, {{ $hasAnyCountedMoveLine ? 'true' : 'false' }}, {{ $shouldAskBackorder ? 'true' : 'false' }})"
                    >
                        {{ $action['label'] }}
                    </x-filament::button>
                @elseif ($action['key'] === 'cancel')
                    <x-filament::button
                        color="danger"
                        class="w-full justify-center"
                        x-on:click="requestAction('{{ $action['key'] }}', '{{ addslashes($action['label']) }}')"
                    >
                        {{ $action['label'] }}
                    </x-filament::button>
                @else
                    <x-filament::button
                        color="gray"
                        class="w-full justify-center"
                        x-on:click="requestAction('{{ $action['key'] }}', '{{ addslashes($action['label']) }}')"
                    >
                        {{ $action['label'] }}
                    </x-filament::button>
                @endif
            @endforeach
        </footer>

        <div class="fixed inset-0 z-30 flex items-end justify-center bg-slate-950/35 sm:items-center sm:p-2"
            x-show="confirmPending"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div class="w-full rounded-t-2xl bg-white p-4 shadow-2xl sm:max-w-3xl sm:rounded-xl"
                x-show="confirmPending"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="translate-y-4 opacity-0 sm:scale-95"
                x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="translate-y-0 opacity-100 sm:scale-100"
                x-transition:leave-end="translate-y-4 opacity-0 sm:scale-95"
            >
                {{-- Backorder warning --}}
                <template x-if="confirmMode === 'backorder'">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-950">{{ __('barcode::app.actions.backorder-title') }}</h3>
                        <p class="mt-1 mb-4 text-sm text-gray-600">{{ __('barcode::app.actions.backorder-prompt') }}</p>

                        <table class="mb-4 w-full border-collapse text-sm">
                            <thead>
                                <tr>
                                    <th class="border-b border-gray-200 px-2 py-3 text-left font-semibold text-gray-500 uppercase tracking-wide">{{ __('barcode::app.actions.backorder-col-product') }}</th>
                                    <th class="border-b border-gray-200 px-2 py-3 text-left font-semibold text-gray-500 uppercase tracking-wide">{{ __('barcode::app.actions.backorder-col-done-todo') }}</th>
                                    <th class="border-b border-gray-200 px-2 py-3 text-left font-semibold text-gray-500 uppercase tracking-wide">{{ __('barcode::app.actions.backorder-col-backorder') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="row in backorderMoveLines" :key="row.id">
                                    <tr>
                                        <td class="border-b border-gray-200 px-2 py-3 text-gray-950" x-text="row.name"></td>
                                        <td class="border-b border-gray-200 px-2 py-3 text-danger-600" x-text="row.counted + ' / ' + row.required + ' ' + row.uom"></td>
                                        <td class="border-b border-gray-200 px-2 py-3 text-gray-950" x-text="row.backorder + ' ' + row.uom"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <div class="grid w-full grid-cols-3 gap-2">
                            <x-filament::button color="gray" class="w-full justify-center" x-on:click="cancelAction()">
                                {{ __('barcode::app.actions.stay-on-transfer') }}
                            </x-filament::button>
                            <x-filament::button color="danger" class="w-full justify-center" x-on:click="$wire.executeAction(confirmPending, true); cancelAction()">
                                {{ __('barcode::app.actions.no-backorder') }}
                            </x-filament::button>
                            <x-filament::button color="primary" class="w-full justify-center" x-on:click="$wire.executeAction(confirmPending, false); cancelAction()">
                                {{ __('barcode::app.actions.validate') }}
                            </x-filament::button>
                        </div>
                    </div>
                </template>

                {{-- Simple confirmation --}}
                <template x-if="confirmMode === 'simple'">
                    <div>
                        <p class="mb-4 text-sm text-gray-700">{{ __('barcode::app.actions.confirm-prompt') }} <strong class="text-gray-950" x-text="confirmLabel"></strong>?</p>
                        <div class="grid w-full grid-cols-2 gap-2">
                            <x-filament::button color="gray" class="w-full justify-center" x-on:click="cancelAction()">
                                {{ __('barcode::app.actions.cancel') }}
                            </x-filament::button>
                            <x-filament::button color="primary" class="w-full justify-center" x-on:click="$wire.executeAction(confirmPending); cancelAction()">
                                {{ __('barcode::app.actions.confirm') }}
                            </x-filament::button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    @endif
</main>
