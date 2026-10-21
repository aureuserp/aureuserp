@php($keyClass = 'flex min-h-[clamp(2.1rem,4.6vh,3.25rem)] items-center justify-center rounded-lg border text-lg font-medium transition-colors')
@php($plain = $keyClass.' border-gray-200 bg-white text-gray-950 hover:bg-gray-50 active:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:hover:bg-gray-800')

<div class="grid flex-none grid-cols-4 gap-[clamp(0.25rem,0.8vh,0.5rem)]">
    @foreach (['1', '2', '3'] as $digit)
        <button type="button" class="{{ $plain }}" x-on:click="press('{{ $digit }}')">{{ $digit }}</button>
    @endforeach

    <div class="col-start-4 row-span-3 row-start-1 flex flex-col overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
        @foreach ([['qty', __('point-of-sale::filament/pos/pages/orders.refund.qty')], ['discount', '%'], ['price', __('point-of-sale::filament/pos/pages/orders.refund.price')]] as $index => $mode)
            <button
                type="button"
                class="flex flex-1 items-center justify-center text-sm font-medium transition-colors disabled:cursor-not-allowed disabled:text-gray-300 dark:disabled:text-gray-600 {{ $index < 2 ? 'border-b border-gray-200 dark:border-gray-700' : '' }}"
                :class="mode === '{{ $mode[0] }}'
                    ? 'bg-primary-100 font-semibold text-primary-900 dark:bg-primary-500/25 dark:text-primary-100'
                    : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800'"
                @disabled($mode[0] !== 'qty')
                x-on:click="mode = '{{ $mode[0] }}'"
            >
                {{ $mode[1] }}
            </button>
        @endforeach
    </div>

    @foreach (['4', '5', '6', '7', '8', '9'] as $digit)
        <button type="button" class="{{ $plain }}" x-on:click="press('{{ $digit }}')">{{ $digit }}</button>
    @endforeach

    <button
        type="button"
        class="{{ $keyClass }} border-warning-200 bg-warning-100 text-warning-900 hover:bg-warning-200 dark:border-warning-500/30 dark:bg-warning-500/20 dark:text-warning-200"
        x-on:click="press('clear')"
    >
        C
    </button>

    <button type="button" class="{{ $plain }}" x-on:click="press('0')">0</button>

    <button
        type="button"
        class="{{ $keyClass }} border-info-200 bg-info-100 text-info-900 hover:bg-info-200 dark:border-info-500/30 dark:bg-info-500/20 dark:text-info-200"
        x-on:click="press('.')"
    >
        .
    </button>

    <button
        type="button"
        class="{{ $keyClass }} border-danger-200 bg-danger-100 text-danger-800 hover:bg-danger-200 dark:border-danger-500/30 dark:bg-danger-500/20 dark:text-danger-200"
        aria-label="{{ __('point-of-sale::filament/pos/pages/orders.refund.backspace') }}"
        x-on:click="press('backspace')"
    >
        &#9003;
    </button>
</div>
