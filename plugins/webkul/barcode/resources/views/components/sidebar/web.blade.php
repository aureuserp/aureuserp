@php
    $items = \Webkul\Barcode\Support\Navigation::items();
@endphp

<aside class="flex min-h-screen flex-col border-e border-gray-200 bg-white px-3 py-4 shadow-[0_8px_24px_rgba(15,23,42,0.12)]">
    <div class="mb-3 border-b border-gray-200 px-2 pt-1 pb-3">
        <div class="mb-1 text-xs font-bold text-[var(--primary-600)]">{{ __('barcode::app.title') }}</div>
        <strong class="block text-lg leading-6 font-semibold text-gray-950">{{ __('barcode::app.navigation.label') }}</strong>
    </div>

    <nav class="flex flex-col gap-1.5" aria-label="Barcode navigation">
        @foreach ($items as $item)
            @if ($item['disabled'])
                <button type="button" class="grid min-h-12 w-full grid-cols-[20px_minmax(0,1fr)] gap-x-2.5 gap-y-1 rounded-xl border border-transparent px-3 py-2.5 text-left text-gray-600 opacity-70" disabled aria-disabled="true">
                    <x-filament::icon :icon="$item['icon']" class="row-span-2 h-[18px] w-[18px]" />
                    <span class="col-start-2 text-sm font-medium">{{ $item['label'] }}</span>
                    <small class="col-start-2 text-[11px] leading-4 text-gray-500">{{ __('barcode::app.navigation.coming-soon') }}</small>
                </button>
            @else
                <a
                    href="{{ $item['href'] }}"
                    wire:navigate
                    @if ($item['active']) aria-current="page" @endif
                    @class([
                        'grid min-h-12 w-full grid-cols-[20px_minmax(0,1fr)] gap-x-2.5 gap-y-1 rounded-xl border px-3 py-2.5 text-left text-sm font-medium no-underline transition-colors duration-150 outline-none focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--primary-200)] focus-visible:ring-offset-2',
                        'border-[var(--primary-200)] bg-[var(--primary-50)] text-[var(--primary-600)]' => $item['active'],
                        'border-transparent bg-transparent text-gray-950 hover:border-gray-200 hover:bg-gray-50 active:bg-gray-100' => ! $item['active'],
                    ])
                >
                    <x-filament::icon :icon="$item['icon']" class="row-span-2 h-[18px] w-[18px]" />
                    <span class="col-start-2">{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>
</aside>
