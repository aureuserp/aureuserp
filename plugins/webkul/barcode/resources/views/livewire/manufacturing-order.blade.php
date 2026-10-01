@php
    $producingQuantity = max((float) $quantityProducing, 0);
    $remainingToProduce = max($remainingQuantity - $producingQuantity, 0);
    $canDecrementProducing = $producingQuantity > 0;
    $canProduceRemaining = $remainingToProduce > 0;
@endphp

<main class="min-h-screen bg-gray-50 p-2" x-data="barcodeScanner('barcode', 'scan')">
    @unless (\Webkul\Barcode\Support\NativeApp::usesNativeNavigation())
        @include('barcode::components.header.web', [
            'title' => $order->name,
            'subtitle' => $order->product?->name,
            'breadcrumbs' => [
                ['label' => __('barcode::app.title'), 'href' => route('barcode.dashboard')],
                ['label' => __('barcode::app.manufacturing.title'), 'href' => route('barcode.manufacturing-orders')],
            ],
            'showCancel' => $editingComponent ? true : null,
            'showBarcode' => $editingComponent ? null : true,
            'cancelAction' => 'discardComponentEdit',
        ])
    @endunless

    @if ($editingComponent)
        @php
            $componentDemand = (float) $editingComponent->product_uom_qty;
            $componentCounted = max((float) $editingComponentQuantity, 0);
            $componentRemaining = max($componentDemand - $componentCounted, 0);
            $canDecrementComponent = $componentCounted > 0;
            $canConsumeRemaining = $componentRemaining > 0;
        @endphp

        <section class="mx-auto">
            <x-filament::section compact class="mb-3">
                <div class="flex min-w-0 flex-col gap-1">
                    <strong class="block text-xl leading-6 font-medium text-gray-950">{{ $editingComponent->product?->name }}</strong>
                    @if ($editingComponent->product?->reference)
                        <span class="text-sm leading-5 text-gray-600">[{{ $editingComponent->product->reference }}]</span>
                    @endif
                </div>
            </x-filament::section>

            <form wire:submit="confirmComponentEdit">
                <x-filament::section compact class="mb-3">
                    <x-slot name="heading">
                        {{ __('barcode::app.manufacturing.component-editor-title') }}
                    </x-slot>

                    <x-slot name="description">
                        {{ __('barcode::app.manufacturing.component-editor-subtitle') }}
                    </x-slot>

                    <div class="grid grid-cols-[minmax(0,1fr)_120px] gap-3">
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="number"
                                min="0"
                                step="0.01"
                                wire:model.live.debounce.500ms="editingComponentQuantity"
                            />
                        </x-filament::input.wrapper>
                        <div class="flex min-h-10 items-center rounded-md border border-gray-200 bg-gray-100 px-4 text-base text-gray-950">{{ $editingComponent->uom?->name }}</div>
                    </div>

                    <div class="mt-3 grid grid-cols-4 gap-2">
                        <x-filament::button color="gray" class="w-full justify-center" type="button" wire:click="setEditingComponentQuantity(0)">0</x-filament::button>
                        <x-filament::button color="gray" class="w-full justify-center" type="button" wire:click="adjustEditingComponentQuantity(-1)" :disabled="! $canDecrementComponent">-1</x-filament::button>
                        <x-filament::button color="gray" class="w-full justify-center" type="button" wire:click="adjustEditingComponentQuantity(1)">+1</x-filament::button>
                        <x-filament::button color="success" class="w-full justify-center" type="button" wire:click="setEditingComponentQuantity({{ $componentDemand }})" :disabled="! $canConsumeRemaining">
                            +{{ number_format($componentRemaining, 0) }}
                        </x-filament::button>
                    </div>

                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-xs">
                            <span class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('barcode::app.manufacturing.to-consume') }}</span>
                            <strong class="mt-1 block text-sm font-semibold text-gray-950">{{ number_format($componentDemand, 2) }} {{ $editingComponent->uom?->name }}</strong>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-xs">
                            <span class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('barcode::app.manufacturing.consumed') }}</span>
                            <strong class="mt-1 block text-sm font-semibold text-gray-950">{{ number_format($componentCounted, 2) }} {{ $editingComponent->uom?->name }}</strong>
                        </div>
                    </div>
                </x-filament::section>
            </form>
        </section>

        <footer class="fixed inset-x-0 bottom-0 z-20 grid grid-cols-2 gap-2 border-t border-gray-200 bg-white px-2 py-2 shadow-[0_-4px_16px_rgba(15,23,42,0.08)]" style="padding-bottom: calc(0.5rem + var(--inset-bottom, 0px))">
            <x-filament::button color="gray" class="w-full justify-center" wire:click="discardComponentEdit">
                {{ __('barcode::app.operation.discard') }}
            </x-filament::button>
            <x-filament::button color="primary" class="w-full justify-center" wire:click="confirmComponentEdit">
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
                    wire:model.live.debounce.250ms="barcode"
                    :placeholder="__('barcode::app.manufacturing.scan')"
                    autocomplete="off"
                />
            </x-filament::input.wrapper>
        </form>

        @if ($blockedNotice)
            <x-filament::callout icon="heroicon-o-lock-closed" color="warning" class="mb-3">
                <x-slot name="heading">
                    {{ $order->state?->getLabel() }}
                </x-slot>

                <x-slot name="description">
                    {{ $blockedNotice }}
                </x-slot>
            </x-filament::callout>
        @endif

        @if ($notice)
            <x-filament::callout icon="heroicon-o-information-circle" :color="$noticeColor" class="mb-3">
                <x-slot name="heading">
                    {{ $order->name }}
                </x-slot>

                <x-slot name="description">
                    {{ $notice }}
                </x-slot>
            </x-filament::callout>
        @endif

        <x-filament::section compact class="mb-3">
            <div class="flex items-start justify-between gap-4">
                <div class="flex min-w-0 flex-col gap-1">
                    <strong class="block text-xl leading-6 font-medium text-gray-950">{{ $order->product?->name }}</strong>
                    <span class="text-sm leading-5 text-gray-600">{{ $order->billOfMaterial?->reference ?? $order->billOfMaterial?->name }}</span>
                    @if ($order->producingLot?->name)
                        <span class="text-sm leading-5 text-gray-600">{{ __('barcode::app.adjustments.lot-serial') }}: {{ $order->producingLot->name }}</span>
                    @endif
                </div>

                @if ($order->state)
                    <x-filament::badge :color="$order->state->getColor()">
                        {{ $order->state->getLabel() }}
                    </x-filament::badge>
                @endif
            </div>

            <div class="mt-4 grid grid-cols-[minmax(0,1fr)_120px] gap-3">
                <x-filament::input.wrapper>
                    <x-filament::input
                        type="number"
                        min="0"
                        step="0.01"
                        wire:model.live.debounce.500ms="quantityProducing"
                    />
                </x-filament::input.wrapper>
                <div class="flex min-h-10 items-center rounded-md border border-gray-200 bg-gray-100 px-4 text-base text-gray-950">{{ $order->uom?->name }}</div>
            </div>

            <div class="mt-3 grid grid-cols-4 gap-2">
                <x-filament::button color="gray" class="w-full justify-center" type="button" wire:click="setQuantityProducing(0)">0</x-filament::button>
                <x-filament::button color="gray" class="w-full justify-center" type="button" wire:click="adjustQuantityProducing(-1)" :disabled="! $canDecrementProducing">-1</x-filament::button>
                <x-filament::button color="gray" class="w-full justify-center" type="button" wire:click="adjustQuantityProducing(1)">+1</x-filament::button>
                <x-filament::button color="success" class="w-full justify-center" type="button" wire:click="setQuantityProducing({{ $remainingQuantity }})" :disabled="! $canProduceRemaining">
                    +{{ number_format($remainingToProduce, 0) }}
                </x-filament::button>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-xs">
                    <span class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('barcode::app.manufacturing.to-produce') }}</span>
                    <strong class="mt-1 block text-sm font-semibold text-gray-950">{{ number_format((float) $order->quantity, 2) }} {{ $order->uom?->name }}</strong>
                </div>
                <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-xs">
                    <span class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('barcode::app.manufacturing.producing') }}</span>
                    <strong class="mt-1 block text-sm font-semibold text-gray-950">{{ number_format($producingQuantity, 2) }} {{ $order->uom?->name }}</strong>
                </div>
                <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-xs">
                    <span class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('barcode::app.manufacturing.source-location') }}</span>
                    <strong class="mt-1 block text-sm font-semibold text-gray-950">{{ $order->sourceLocation?->full_name ?? '—' }}</strong>
                </div>
            </div>

            <div class="mt-3">
                <x-filament::button color="gray" outlined class="w-full justify-center" type="button" wire:click="produceAll">
                    {{ __('barcode::app.manufacturing.produce-all') }}
                </x-filament::button>
            </div>
        </x-filament::section>

        <section class="grid gap-2 pb-24">
            <div class="text-sm font-semibold uppercase tracking-wide text-gray-950">{{ __('barcode::app.manufacturing.components') }}</div>

            @forelse ($components as $componentMove)
                @php
                    $demandQuantity = (float) $componentMove->product_uom_qty;
                    $consumedQuantity = max((float) ($componentQuantities[$componentMove->id] ?? 0), 0);
                    $componentState = $consumedQuantity <= 0
                        ? ''
                        : ($consumedQuantity >= $demandQuantity ? 'is-complete' : 'is-partial');
                @endphp

                <article
                    id="component-{{ $componentMove->id }}"
                    wire:key="component-{{ $componentMove->id }}"
                    @class([
                        'flex items-start justify-between gap-3 rounded-lg border shadow-xs',
                        'border-gray-200 bg-white' => $componentState === '',
                        'border-[var(--success-500)] bg-[var(--success-50)]' => $componentState === 'is-complete',
                        'border-[var(--warning-500)] bg-[var(--warning-50)]' => $componentState === 'is-partial',
                    ])
                >
                    <div class="min-w-0 flex-1 px-4 py-4">
                        <div class="flex flex-col gap-1">
                            <strong class="block text-base leading-5 font-semibold text-gray-950">{{ $componentMove->product?->name }}</strong>
                            @if ($componentMove->product?->reference)
                                <span class="text-sm leading-5 text-gray-600">[{{ $componentMove->product->reference }}]</span>
                            @endif
                            <div class="mt-4 flex items-baseline gap-1.5">
                                <strong @class([
                                    'text-[32px] leading-none font-medium',
                                    'text-gray-950' => $componentState === '',
                                    'text-[var(--warning-600)]' => $componentState === 'is-partial',
                                    'text-[var(--success-600)]' => $componentState === 'is-complete',
                                ])>{{ number_format($consumedQuantity, 0) }} / {{ number_format($demandQuantity, 0) }}</strong>
                                <span class="text-sm font-bold text-gray-950">{{ $componentMove->uom?->name }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-col items-end gap-7 px-4 py-3 text-right">
                        <x-filament::button
                            color="gray"
                            outlined
                            icon="heroicon-m-pencil-square"
                            class="h-[42px] w-[42px] justify-center"
                            wire:click="editComponent({{ $componentMove->id }})"
                            tooltip="{{ __('barcode::app.manufacturing.component-edit-tooltip') }}"
                        />

                        <div class="flex min-h-[42px] items-end justify-end gap-1.5">
                            @if ($consumedQuantity <= 0)
                                <x-filament::button color="gray" outlined type="button" class="h-[42px] min-w-[42px] justify-center px-3" wire:click="setComponentQuantity({{ $componentMove->id }}, {{ $demandQuantity }})">+{{ number_format($demandQuantity, 0) }}</x-filament::button>
                            @elseif ($consumedQuantity >= $demandQuantity)
                                <x-filament::button color="gray" outlined type="button" class="h-[42px] min-w-[42px] justify-center px-3" wire:click="adjustComponentQuantity({{ $componentMove->id }}, -1)">-1</x-filament::button>
                            @else
                                <x-filament::button color="gray" outlined type="button" class="h-[42px] min-w-[42px] justify-center px-3" wire:click="adjustComponentQuantity({{ $componentMove->id }}, 1)">+1</x-filament::button>
                                <x-filament::button color="gray" outlined type="button" class="h-[42px] min-w-[42px] justify-center px-3" wire:click="adjustComponentQuantity({{ $componentMove->id }}, -1)">-1</x-filament::button>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="flex flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-gray-300 bg-white px-6 py-10 text-center text-gray-600">
                    <x-filament::icon icon="heroicon-o-inbox" class="h-8 w-8 text-gray-400" />
                    <div>{{ __('barcode::app.manufacturing.empty-components') }}</div>
                </div>
            @endforelse
        </section>

        @if ($actions !== [])
            <footer class="fixed inset-x-0 bottom-0 z-20 grid auto-cols-fr grid-flow-col gap-2 border-t border-gray-200 bg-white px-2 py-2 shadow-[0_-4px_16px_rgba(15,23,42,0.08)]" style="padding-bottom: calc(0.5rem + var(--inset-bottom, 0px))">
                @foreach ($actions as $action)
                    <x-filament::button
                        :color="$action['variant']"
                        class="w-full justify-center"
                        x-on:click="requestAction('{{ $action['key'] }}', '{{ addslashes($action['label']) }}')"
                    >
                        {{ $action['label'] }}
                    </x-filament::button>
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
                <div class="w-full rounded-t-2xl bg-white p-4 shadow-2xl sm:max-w-3xl sm:rounded-xl">
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
            </div>
        @endif
    @endif
</main>
