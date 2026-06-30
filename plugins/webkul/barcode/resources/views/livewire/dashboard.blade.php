<div>
    <main class="min-h-screen p-2">
        @unless (\Webkul\Barcode\Support\NativeApp::usesNativeNavigation())
            @include('barcode::components.header.web', [
                'title' => __('barcode::app.dashboard.operations'),
                'breadcrumbs' => [
                    ['label' => __('barcode::app.title')],
                ],
            ])
        @endunless

        <section class="grid grid-cols-[repeat(auto-fill,minmax(260px,1fr))] gap-2">
            @forelse ($operationTypes as $operationType)
                <a class="flex min-h-[72px] items-center justify-between rounded-lg border border-gray-200 bg-white px-3 text-gray-950 no-underline shadow-xs" href="{{ route('barcode.transfers', $operationType) }}" wire:navigate>
                    <div class="flex min-w-0 flex-col gap-0.5">
                        <span>{{ $operationType->name }}</span>

                        @if ($operationType->warehouse?->name)
                            <small class="truncate text-sm font-bold text-gray-600 normal-case">{{ $operationType->warehouse->name }}</small>
                        @endif
                    </div>

                    <strong class="min-w-6 rounded-full bg-gray-100 px-2 py-1 text-center">{{ $operationType->waiting_count }}</strong>
                </a>
            @empty
                <div class="flex flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-gray-300 bg-white px-6 py-10 text-center text-gray-600">
                    <x-filament::icon icon="heroicon-o-inbox" class="h-8 w-8 text-gray-400" />
                    <div>{{ __('barcode::app.dashboard.empty') }}</div>
                </div>
            @endforelse
        </section>
    </main>
</div>
