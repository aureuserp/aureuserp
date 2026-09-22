<div>
    <main class="min-h-screen bg-gray-50 p-2" x-data="barcodeScanner('search', 'openOrder')">
        @unless (\Webkul\Barcode\Support\NativeApp::usesNativeNavigation())
            @include('barcode::components.header.web', [
                'title' => __('barcode::app.manufacturing.title'),
                'breadcrumbs' => [
                    ['label' => __('barcode::app.title'), 'href' => route('barcode.dashboard')],
                    ['label' => __('barcode::app.manufacturing.title')],
                ],
                'showBarcode' => true,
            ])
        @endunless

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

        <form class="mb-3" wire:submit="openOrder">
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
                    :placeholder="__('barcode::app.manufacturing.search')"
                    autocomplete="off"
                />
            </x-filament::input.wrapper>
        </form>

        @if ($notice)
            <x-filament::callout icon="heroicon-o-information-circle" :color="$noticeColor" class="mb-3">
                <x-slot name="heading">
                    {{ __('barcode::app.manufacturing.title') }}
                </x-slot>

                <x-slot name="description">
                    {{ $notice }}
                </x-slot>
            </x-filament::callout>
        @endif

        <section class="grid gap-2 pb-6">
            @forelse ($orders as $order)
                <a class="flex items-start justify-between gap-3 rounded-lg border border-gray-200 bg-white px-4 py-3 text-gray-950 no-underline shadow-xs" href="{{ route('barcode.manufacturing-order', $order) }}" wire:navigate>
                    <div class="min-w-0">
                        <strong class="block text-base leading-5 font-semibold">{{ $order->name }}</strong>
                        <span class="mt-1 block text-sm text-gray-600">{{ $order->product?->name }}</span>
                        <span class="mt-1 block text-sm font-semibold text-gray-950">{{ number_format((float) $order->quantity, 2) }} {{ $order->uom?->name }}</span>
                    </div>

                    <div class="flex shrink-0 flex-col items-end gap-2 text-right">
                        @if ($order->state)
                            <x-filament::badge :color="$order->state->getColor()">
                                {{ $order->state->getLabel() }}
                            </x-filament::badge>
                        @endif
                        <time class="text-xs text-gray-500">{{ $order->deadline_at?->format('M d') }}</time>
                    </div>
                </a>
            @empty
                <div class="flex flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-gray-300 bg-white px-6 py-10 text-center text-gray-600">
                    <x-filament::icon icon="heroicon-o-inbox" class="h-8 w-8 text-gray-400" />
                    <div>{{ __('barcode::app.manufacturing.empty') }}</div>
                </div>
            @endforelse

            @if ($orders->isNotEmpty())
                <div class="flex flex-col items-center gap-2 py-2 text-sm text-gray-600">
                    <span>{{ __('barcode::app.pagination.showing', ['shown' => $orders->count(), 'total' => $totalOrders]) }}</span>

                    @if ($orders->count() < $totalOrders)
                        <x-filament::button color="gray" outlined type="button" wire:click="loadMore">
                            {{ __('barcode::app.pagination.load-more') }}
                        </x-filament::button>
                    @endif
                </div>
            @endif
        </section>
    </main>
</div>
