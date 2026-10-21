<x-filament-panels::page>
    @php($orders = $this->getOrders())
    @php($selected = $this->getSelectedOrder())
    @php($prefix = 'point-of-sale::filament/pos/pages/orders.')
    @php($isRestaurant = $this->isRestaurant())

    <div class="pos-screen fixed inset-x-0 bottom-0 top-16 grid min-h-0 grid-cols-[1fr] grid-rows-[minmax(0,1fr)] gap-4 p-4 lg:grid-cols-[1fr_minmax(22rem,32%)]">
        <section class="flex min-h-0 min-w-0 flex-col gap-3">
            <div class="flex flex-none items-center gap-2">
                <x-filament::button
                    color="gray"
                    icon="heroicon-m-chevron-left"
                    tag="a"
                    :href="$this->backUrl()"
                >
                    {{ __($prefix.'actions.back') }}
                </x-filament::button>

                <x-filament::input.wrapper prefix-icon="heroicon-o-magnifying-glass" class="min-w-0 flex-1">
                    <x-filament::input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        :placeholder="__($prefix.($isRestaurant ? 'search-restaurant' : 'search'))"
                    />
                </x-filament::input.wrapper>

                <x-filament::input.wrapper class="flex-none">
                    <x-filament::input.select wire:model.live="status">
                        @foreach ($this->statusOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>

                <span class="flex-none font-mono text-sm tabular-nums text-gray-600 dark:text-gray-300">
                    {{ $orders->currentPage() }}/{{ max($orders->lastPage(), 1) }}
                </span>

                <x-filament::icon-button
                    icon="heroicon-m-chevron-left"
                    :disabled="$orders->onFirstPage()"
                    wire:click="previousPage"
                    :label="__($prefix.'actions.previous')"
                />

                <x-filament::icon-button
                    icon="heroicon-m-chevron-right"
                    :disabled="! $orders->hasMorePages()"
                    wire:click="nextPage"
                    :label="__($prefix.'actions.next')"
                />
            </div>

            <div class="min-h-0 flex-auto overflow-auto rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 z-[1] bg-gray-700 text-xs uppercase tracking-wide text-white dark:bg-gray-800">
                        <tr>
                            <th class="px-3 py-2 text-start font-medium">{{ __($prefix.'columns.date') }}</th>
                            <th class="px-3 py-2 text-start font-medium">{{ __($prefix.'columns.receipt') }}</th>
                            <th class="px-3 py-2 text-start font-medium">{{ __($prefix.'columns.order') }}</th>
                            <th class="px-3 py-2 text-start font-medium">{{ __($prefix.'columns.customer') }}</th>
                            <th class="px-3 py-2 text-start font-medium">{{ __($prefix.'columns.cashier') }}</th>
                            <th class="px-3 py-2 text-start font-medium">{{ __($prefix.'columns.tracking') }}</th>

                            @if ($isRestaurant)
                                <th class="px-3 py-2 text-start font-medium">{{ __($prefix.'columns.table') }}</th>
                            @endif
                            <th class="px-3 py-2 text-center font-medium">{{ __($prefix.'columns.total') }}</th>
                            <th class="px-3 py-2 text-center font-medium">{{ __($prefix.'columns.status') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($orders as $order)
                            <tr
                                wire:key="order-{{ $order->getKey() }}"
                                @class([
                                    'cursor-pointer border-b border-gray-100 transition-colors dark:border-gray-800',
                                    'bg-primary-600 text-white' => $selected?->getKey() === $order->getKey(),
                                    'odd:bg-gray-50 hover:bg-gray-100 dark:odd:bg-gray-800/40 dark:hover:bg-gray-800' => $selected?->getKey() !== $order->getKey(),
                                ])
                                wire:click="selectOrder({{ $order->getKey() }})"
                            >
                                <td class="whitespace-nowrap px-3 py-2 font-mono text-xs tabular-nums">
                                    {{ $order->ordered_at?->format('d/m/Y H:i:s') }}
                                </td>

                                <td class="whitespace-nowrap px-3 py-2 font-mono text-xs">{{ $order->reference }}</td>

                                <td class="whitespace-nowrap px-3 py-2">{{ $order->name }}</td>

                                <td class="max-w-40 truncate px-3 py-2">
                                    {{ $order->partner?->name ?? __($prefix.'walk-in') }}
                                </td>

                                <td class="max-w-32 truncate px-3 py-2">{{ $order->user?->name }}</td>

                                <td class="whitespace-nowrap px-3 py-2 font-mono text-xs tabular-nums">{{ $order->tracking_number ?? '—' }}</td>

                                @if ($isRestaurant)
                                    <td class="whitespace-nowrap px-3 py-2">
                                        {{ $order->table ? collect([$order->table->floor?->name, $order->table->table_number])->filter(fn ($part) => filled($part))->implode('/') : '—' }}
                                    </td>
                                @endif

                                <td class="whitespace-nowrap px-3 py-2 text-center font-mono tabular-nums font-semibold">
                                    {{ $this->money((float) $order->amount_total, $order) }}
                                </td>

                                <td class="px-3 py-2 text-center">{{ $order->state->getLabel() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isRestaurant ? 9 : 8 }}" class="px-3 py-10 text-center text-gray-500 dark:text-gray-400">
                                    {{ __($prefix.'empty.heading') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="flex min-h-0 min-w-0 flex-col">
            @if ($selected)
                @php($canRefund = $this->canRefund($selected))

                <div
                    wire:key="refund-panel-{{ $selected->getKey() }}"
                    class="flex min-h-0 flex-auto flex-col gap-2"
                    x-data="{
                        mode: 'qty',
                        buffer: '',
                        active: null,
                        quantities: @js((object) $this->refundQuantities($selected)),
                        select(lineId) {
                            if (! this.quantities[lineId]) return
                            this.active = lineId
                            this.buffer = ''
                        },
                        typed(event) {
                            if (this.active === null || event.ctrlKey || event.metaKey || event.altKey) return

                            if (event.target.closest('input, textarea, select, [contenteditable]')) return

                            const key = { Backspace: 'backspace', Delete: 'clear', ',': '.' }[event.key] ?? event.key

                            if (! /^[0-9.]$/.test(key) && ! ['backspace', 'clear'].includes(key)) return

                            event.preventDefault()

                            this.press(key)
                        },
                        press(key) {
                            const line = this.quantities[this.active]

                            if (! line) return

                            if (key === 'backspace') {
                                this.buffer = this.buffer.slice(0, -1)
                            } else if (key === 'clear') {
                                this.buffer = ''
                            } else {
                                this.buffer += key
                            }

                            const requested = Number(this.buffer || 0)

                            if (requested > line.refundable) {
                                if (window.FilamentNotification) {
                                    new window.FilamentNotification()
                                        .title(@js(__($prefix.'refund.max-exceeded.title')))
                                        .body(
                                            @js(__($prefix.'refund.max-exceeded.body'))
                                                .replace(':requested', requested)
                                                .replace(':max', line.refundable)
                                        )
                                        .warning()
                                        .send()
                                }

                                this.buffer = ''

                                return
                            }

                            line.qty = Math.max(requested, 0)
                        },
                        get total() {
                            return Object.values(this.quantities).reduce((sum, line) => sum + Number(line.qty || 0), 0)
                        },
                        payload() {
                            const out = {}

                            for (const [id, line] of Object.entries(this.quantities)) {
                                if (Number(line.qty) > 0) out[id] = Number(line.qty)
                            }

                            return out
                        },
                    }"
                    x-on:pos-refund-lines.window="quantities = $event.detail.quantities ?? {}; active = null; buffer = ''"
                    x-on:keydown.window="typed($event)"
                >
                    @if ($canRefund)
                        <p class="flex-none rounded-lg border border-gray-200 bg-white px-3 py-2 text-center text-sm text-danger-600 dark:border-gray-700 dark:bg-gray-900 dark:text-danger-400">
                            {{ __($prefix.'refund.prompt') }}
                        </p>
                    @endif

                    <div class="flex min-h-0 flex-auto flex-col overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                        <div class="min-h-0 flex-auto overflow-y-auto">
                            @foreach ($selected->lines as $line)
                                @php($image = $this->lineImage($line))

                                <button
                                    type="button"
                                    wire:key="line-{{ $line->getKey() }}"
                                    class="flex w-full items-start gap-2 border-b border-gray-100 px-3 py-2.5 text-start transition-colors disabled:cursor-default dark:border-gray-800"
                                    :class="active === {{ $line->getKey() }}
                                        ? 'bg-primary-50 dark:bg-primary-500/10'
                                        : (quantities[{{ $line->getKey() }}] ? 'hover:bg-gray-50 dark:hover:bg-gray-800' : 'opacity-50')"
                                    x-on:click="select({{ $line->getKey() }})"
                                    @disabled(! $canRefund || float_compare($line->refundableQty(), 0, precisionDigits: 4) <= 0)
                                >
                                    <span class="flex size-10 flex-none items-center justify-center overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800">
                                        @if ($image)
                                            <img src="{{ $image }}" alt="{{ $line->product?->name }}" class="h-full w-full object-cover" />
                                        @else
                                            <x-filament::icon icon="heroicon-o-cube" class="size-5 text-gray-300 dark:text-gray-600" />
                                        @endif
                                    </span>

                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-sm font-medium text-gray-950 dark:text-white">
                                            {{ $line->product?->name }}
                                        </span>

                                        <span class="block font-mono text-xs tabular-nums text-gray-500 dark:text-gray-400">
                                            {{ $line->qty + 0 }} &times; {{ $this->money((float) $line->price_unit, $selected) }}
                                            @if ((float) $line->discount)
                                                &middot; {{ __($prefix.'line-discount', ['discount' => $line->discount + 0]) }}
                                            @endif
                                        </span>

                                        @if (float_compare((float) $line->refunded_qty, 0, precisionDigits: 4) > 0)
                                            <span class="block font-mono text-xs tabular-nums text-gray-500 dark:text-gray-400">
                                                {{ __($prefix.'refund.refunded') }}
                                                {{ number_format((float) $line->refunded_qty, 2) }}
                                            </span>
                                        @endif

                                        <span
                                            x-show="quantities[{{ $line->getKey() }}]?.qty > 0"
                                            x-cloak
                                            class="block font-mono text-xs tabular-nums text-primary-600 dark:text-primary-400"
                                        >
                                            {{ __($prefix.'refund.to-refund') }}
                                            <span x-text="Number(quantities[{{ $line->getKey() }}]?.qty ?? 0).toFixed(2)"></span>
                                        </span>
                                    </span>

                                    <span class="flex-none font-mono text-sm tabular-nums font-semibold text-gray-950 dark:text-white">
                                        {{ $this->money((float) $line->price_subtotal_incl, $selected) }}
                                    </span>
                                </button>
                            @endforeach
                        </div>

                        <div class="flex-none border-t border-gray-100 p-3 dark:border-gray-800">
                            <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                <span>{{ __($prefix.'taxes') }}</span>
                                <span class="font-mono tabular-nums">{{ $this->money((float) $selected->amount_tax, $selected) }}</span>
                            </div>

                            <div class="mt-1 flex items-center justify-between text-lg font-semibold text-gray-950 dark:text-white">
                                <span>{{ __($prefix.'total') }}</span>
                                <span class="font-mono tabular-nums">{{ $this->money((float) $selected->amount_total, $selected) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid flex-none grid-cols-3 gap-[clamp(0.25rem,0.8vh,0.5rem)]">
                        <a
                            href="{{ $this->detailsUrl($selected) }}"
                            target="_blank"
                            class="flex min-h-[clamp(2.5rem,5.5vh,3.5rem)] items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white text-base font-medium text-gray-600 transition-colors hover:bg-gray-50 active:bg-gray-100 dark:active:bg-gray-700 disabled:text-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:disabled:text-gray-600"
                        >
                            <x-filament::icon icon="heroicon-o-pencil-square" class="size-5 flex-none" />

                            {{ __($prefix.'actions.details') }}
                        </a>

                        @if ($this->canCancel($selected))
                            <button
                                type="button"
                                class="flex min-h-[clamp(2.5rem,5.5vh,3.5rem)] items-center justify-center gap-2 rounded-lg border border-danger-200 bg-danger-50 text-base font-medium text-danger-700 transition-colors hover:bg-danger-100 active:bg-danger-200 dark:border-danger-500/30 dark:bg-danger-500/10 dark:text-danger-300 dark:hover:bg-danger-500/20"
                                wire:click="mountAction('cancelOrder', { order: {{ $selected->getKey() }} })"
                            >
                                <x-filament::icon icon="heroicon-o-x-circle" class="size-5 flex-none" />

                                {{ __($prefix.'actions.cancel.label') }}
                            </button>
                        @else
                            <button
                                type="button"
                                class="flex min-h-[clamp(2.5rem,5.5vh,3.5rem)] items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white text-base font-medium text-gray-600 transition-colors hover:bg-gray-50 active:bg-gray-100 dark:active:bg-gray-700 disabled:text-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:disabled:text-gray-600"
                                @disabled(! $this->canInvoice($selected))
                                wire:click="mountAction('invoiceOrder', { order: {{ $selected->getKey() }} })"
                            >
                                <x-filament::icon icon="heroicon-o-document-text" class="size-5 flex-none" />

                                {{ __($prefix.'actions.invoice.label') }}
                            </button>
                        @endif

                        <button type="button" class="flex min-h-[clamp(2.5rem,5.5vh,3.5rem)] items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white text-base font-medium text-gray-600 transition-colors hover:bg-gray-50 active:bg-gray-100 dark:active:bg-gray-700 disabled:text-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:disabled:text-gray-600" x-on:click="window.pointOfSaleReceipt.print('#pos-receipt')">
                            <x-filament::icon icon="heroicon-o-printer" class="size-5 flex-none" />

                            {{ __($prefix.'actions.print') }}
                        </button>
                    </div>

                    @if ($canRefund)
                        @include('point-of-sale::filament.pos.partials.refund-numpad')

                        <button
                            type="button"
                            class="flex min-h-[clamp(2.6rem,5.5vh,3.5rem)] flex-none items-center justify-center rounded-lg bg-primary-600 text-base font-semibold text-white transition-colors hover:bg-primary-700 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:bg-gray-800"
                            x-bind:disabled="total <= 0"
                            x-on:click="$wire.refundOrder(payload())"
                        >
                            {{ __($prefix.'actions.refund') }}
                        </button>
                    @endif
                </div>
            @else
                <div class="flex min-h-0 flex-auto items-center justify-center rounded-xl border border-dashed border-gray-200 p-6 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                    {{ __($prefix.'select-order') }}
                </div>
            @endif
        </aside>
    </div>

    @if ($selected)
        @include('point-of-sale::filament.pos.partials.receipt', ['receipt' => $this->receipt($selected)])
    @endif

    @if ($session)
        <livewire:point-of-sale-register-controls :session="$session" />
    @endif
</x-filament-panels::page>
