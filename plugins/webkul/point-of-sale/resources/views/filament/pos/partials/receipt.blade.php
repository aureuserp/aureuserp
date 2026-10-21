@php($prefix = 'point-of-sale::filament/pos/partials/receipt.')

<div id="pos-receipt" class="pos-receipt mx-auto hidden w-[80mm] bg-white p-3 text-black">
    <div class="text-center">
        @if ($receipt['company']['logo'])
            <img src="{{ $receipt['company']['logo'] }}" alt="{{ $receipt['company']['name'] }}" class="mx-auto mb-2 max-h-16 object-contain" />
        @endif

        <p class="text-lg font-bold">{{ $receipt['company']['name'] }}</p>

        @if ($receipt['company']['phone'])
            <p class="text-xs">{{ __($prefix.'phone') }} {{ $receipt['company']['phone'] }}</p>
        @endif

        @if ($receipt['company']['email'])
            <p class="text-xs">{{ $receipt['company']['email'] }}</p>
        @endif

        @if ($receipt['company']['website'])
            <p class="text-xs underline">{{ $receipt['company']['website'] }}</p>
        @endif
    </div>

    @if ($receipt['header'])
        <p class="mt-2 whitespace-pre-line text-center text-xs">{{ $receipt['header'] }}</p>
    @endif

    @if ($receipt['cashier'])
        <p class="mt-2 text-center text-xs font-semibold">
            {{ __($prefix.'served-by', ['cashier' => $receipt['cashier']]) }}
        </p>
    @endif

    @if ($receipt['tracking_number'])
        <p class="text-center text-3xl font-light leading-tight">{{ $receipt['tracking_number'] }}</p>
    @endif

    @if ($receipt['customer'])
        <p class="mt-2 text-center text-xs">{{ $receipt['customer'] }}</p>
    @endif

    <div class="mt-3 space-y-1">
        @foreach ($receipt['lines'] as $line)
            <div>
                <div class="flex items-start justify-between gap-2 text-sm font-bold">
                    <span class="min-w-0 flex-1">{{ $line['name'] }}</span>
                    <span class="flex-none tabular-nums">{{ money($line['total'], $receipt['currency']) }}</span>
                </div>

                <div class="text-xs tabular-nums">
                    {{ $line['qty'] + 0 }} &times; {{ money($line['price_unit'], $receipt['currency']) }}@if ($line['uom']) / {{ $line['uom'] }}@endif
                    @if ($line['discount'])
                        &middot; &minus;{{ $line['discount'] + 0 }}%
                    @endif
                </div>

                @if ($line['note'])
                    <div class="text-xs italic">{{ $line['note'] }}</div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="my-2 border-t border-dashed border-black"></div>

    <div class="flex items-center justify-between text-sm">
        <span>{{ __($prefix.'untaxed') }}</span>
        <span class="tabular-nums">{{ money($receipt['subtotal'], $receipt['currency']) }}</span>
    </div>

    @foreach ($receipt['taxes'] as $tax)
        <div class="flex items-center justify-between text-sm">
            <span>{{ $tax['name'] }}</span>
            <span class="tabular-nums">{{ money($tax['amount'], $receipt['currency']) }}</span>
        </div>
    @endforeach

    @if ($receipt['rounding'])
        <div class="flex items-center justify-between text-sm">
            <span>{{ __($prefix.'rounding') }}</span>
            <span class="tabular-nums">{{ money($receipt['rounding'], $receipt['currency']) }}</span>
        </div>
    @endif

    <div class="my-2 border-t border-dashed border-black"></div>

    <div class="flex items-center justify-between text-lg font-bold">
        <span>{{ __($prefix.'total') }}</span>
        <span class="tabular-nums">{{ money($receipt['total'], $receipt['currency']) }}</span>
    </div>

    @foreach ($receipt['payments'] as $payment)
        <div class="flex items-center justify-between text-sm">
            <span>{{ $payment['name'] }}</span>
            <span class="tabular-nums">{{ money($payment['amount'], $receipt['currency']) }}</span>
        </div>
    @endforeach

    @if ($receipt['change'] > 0)
        <div class="flex items-center justify-between text-sm font-semibold">
            <span>{{ __($prefix.'change') }}</span>
            <span class="tabular-nums">{{ money($receipt['change'], $receipt['currency']) }}</span>
        </div>
    @endif

    @if ($receipt['footer'])
        <p class="mt-3 whitespace-pre-line text-center text-xs">{{ $receipt['footer'] }}</p>
    @endif

    <div class="mt-3 text-center text-xs">
        <p>{{ __($prefix.'order', ['order' => $receipt['reference'] ?? $receipt['name']]) }}</p>
        <p>{{ $receipt['ordered_at']?->format('d/m/Y H:i:s') }}</p>
    </div>
</div>
