@props([
    'title',
    'subtitle' => null,
    'breadcrumbs' => [],
    'showBarcode' => null,
    'showCancel' => null,
    'cancelAction' => 'discardMoveLineEdit',
])

<header class="sticky top-0 z-10 -mx-2 -mt-2 mb-3 flex min-h-14 items-center gap-3 border-b border-gray-200 bg-white/95 px-3 py-1.5 shadow-xs backdrop-blur-sm">
    <x-filament::icon-button
        color="gray"
        icon="heroicon-m-bars-3"
        :label="__('barcode::app.navigation.open')"
        x-on:click="sidebarOpen = true"
        class="h-10 w-10 shrink-0"
    />

    <div class="min-w-0">
        @if ($breadcrumbs !== [])
            <div class="flex flex-wrap items-center gap-1 text-xs font-bold text-[var(--primary-600)]">
                @foreach ($breadcrumbs as $breadcrumb)
                    @if (! $loop->first)
                        <span class="text-gray-500">/</span>
                    @endif

                    @if (! empty($breadcrumb['href']))
                        <a href="{{ $breadcrumb['href'] }}" wire:navigate class="text-[var(--primary-600)] hover:underline">{{ $breadcrumb['label'] }}</a>
                    @else
                        <span class="text-gray-500">{{ $breadcrumb['label'] }}</span>
                    @endif
                @endforeach
            </div>
        @endif

        <h1 class="text-base leading-5 font-semibold text-gray-950">{{ $title }}</h1>

        @if (filled($subtitle))
            <p class="mt-0.5 text-xs text-gray-600">{{ $subtitle }}</p>
        @endif
    </div>

    @if (filled($showBarcode))
        <div class="ml-auto flex items-center gap-2">
            <x-filament::icon-button
                color="gray"
                icon="heroicon-m-qr-code"
                :label="__('barcode::app.operation.scan')"
                x-on:click="toggle($wire)"
                x-bind:class="active ? 'bg-[var(--primary-50)] text-[var(--primary-600)] ring-1 ring-[var(--primary-200)]' : 'text-[var(--primary-600)]'"
                class="h-10 w-10 shrink-0 text-[var(--primary-600)]"
            />
        </div>
    @endif

    @if (filled($showCancel))
        <div class="ml-auto flex items-center gap-2">
            <x-filament::icon-button
                color="gray"
                icon="heroicon-m-x-mark"
                :label="__('barcode::app.navigation.back')"
                wire:click="{{ $cancelAction }}"
                class="h-10 w-10 shrink-0"
            />
        </div>
    @endif
</header>
