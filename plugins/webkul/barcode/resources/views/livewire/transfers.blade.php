<div>
    <main class="min-h-screen p-2" x-data="barcodeScanner('search', 'openOperation')">
        @unless (\Webkul\Barcode\Support\NativeApp::usesNativeNavigation())
            @include('barcode::components.header.web', [
                'title' => $operationType->name,
                'breadcrumbs' => [
                    ['label' => __('barcode::app.title'), 'href' => route('barcode.dashboard')],
                    ['label' => __('barcode::app.dashboard.operations')],
                ],
                'showBarcode' => true,
            ])
        @endunless

        <div id="barcode-reader" class="fixed inset-x-2 top-2 z-40 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg" x-show="active" x-cloak></div>

        <div class="mb-3" x-show="scannerError" x-cloak>
            <x-filament::callout icon="heroicon-o-exclamation-triangle" color="warning">
                <x-slot name="heading">
                    Camera unavailable
                </x-slot>

                <x-slot name="description">
                    <span x-text="scannerError"></span>
                </x-slot>
            </x-filament::callout>
        </div>

        <div class="mb-3">
            <form wire:submit="openOperation">
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
                    :placeholder="__('barcode::app.navigation.search')"
                />
            </x-filament::input.wrapper>
            </form>
        </div>

        @if ($operationNotice)
            <x-filament::callout icon="heroicon-o-information-circle" :color="$operationNoticeColor" class="mb-3">
                <x-slot name="heading">
                    {{ __('barcode::app.operation-search.open') }}
                </x-slot>

                <x-slot name="description">
                    {{ $operationNotice }}
                </x-slot>
            </x-filament::callout>
        @endif

        @if ($transfers->isEmpty())
            <section class="flex justify-center pt-3">
                <x-filament::empty-state
                    icon="heroicon-o-inbox"
                    :heading="__('barcode::app.transfers.empty')"
                >
                    <x-slot name="description">
                        {{ __('barcode::app.operation-search.placeholder') }}
                    </x-slot>
                </x-filament::empty-state>
            </section>
        @else
            <section class="grid gap-2">
                @foreach ($transfers as $transfer)
                    <a class="flex items-start justify-between gap-3 rounded-lg border border-gray-200 bg-white px-4 py-3 text-gray-950 no-underline shadow-xs" href="{{ route('barcode.operation', [$operationType, $transfer, 'scan' => $search]) }}" wire:navigate>
                        <div class="min-w-0">
                            <strong class="block text-base leading-5 font-semibold">{{ $transfer->name }}</strong>
                            <span class="mt-1 block text-sm text-gray-600">{{ $transfer->partner?->name ?? $transfer->origin }}</span>
                        </div>
                        <div class="flex shrink-0 flex-col items-end gap-2 text-right">
                            @if ($transfer->state)
                                <x-filament::badge :color="$transfer->state->getColor()">
                                    {{ $transfer->state->getLabel() }}
                                </x-filament::badge>
                            @endif
                            <time class="text-xs text-gray-500">{{ $transfer->scheduled_at?->format('M d') }}</time>
                        </div>
                    </a>
                @endforeach
            </section>
        @endif
    </main>
</div>
