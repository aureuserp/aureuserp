@php($control = $this->closingControl())

<x-filament::modal id="pos-closing" width="2xl">
    <x-slot name="heading">
        {{ __('point-of-sale::filament/pos/pages/terminal.closing.heading') }}
    </x-slot>

    <x-slot name="description">
        {{ __('point-of-sale::filament/pos/pages/terminal.closing.orders', ['quantity' => $control['orders_details']['quantity']]) }}:
        <span class="font-mono tabular-nums">{{ $this->money($control['orders_details']['amount']) }}</span>
    </x-slot>

    <div class="flex flex-col gap-5">
        @if ($control['default_cash_details'])
            @php($cash = $control['default_cash_details'])

            <div class="flex flex-col gap-1 border-b border-gray-100 pb-3 last-of-type:border-b-0 last-of-type:pb-0 dark:border-gray-800">
                <div class="flex items-center justify-between gap-4 text-base font-semibold text-gray-950 dark:text-gray-50">
                    <span>{{ $cash['name'] }}</span>
                    <span class="font-mono tabular-nums">{{ $this->money($cash['amount']) }}</span>
                </div>

                <div class="flex items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <span>{{ __('point-of-sale::filament/pos/pages/terminal.closing.opening') }}</span>
                    <span class="font-mono tabular-nums">{{ $this->money($cash['opening']) }}</span>
                </div>

                <div class="flex items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <span>{{ __('point-of-sale::filament/pos/pages/terminal.closing.payments') }}</span>
                    <span class="font-mono tabular-nums">{{ $this->money($cash['payment_amount']) }}</span>
                </div>

                <button type="button" class="flex items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400 w-full text-start" wire:click="toggleCashMoves">
                    <span class="flex items-center gap-1">
                        <x-filament::icon
                            :icon="$showCashMoves ? 'heroicon-m-chevron-down' : 'heroicon-m-chevron-right'"
                            class="h-4 w-4"
                        />

                        {{ __('point-of-sale::filament/pos/pages/terminal.closing.moves') }}
                    </span>

                    <span class="font-mono tabular-nums">{{ $this->money(collect($cash['moves'])->sum('amount')) }}</span>
                </button>

                @if ($showCashMoves)
                    @foreach ($cash['moves'] as $move)
                        <div class="flex items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400 ps-5">
                            <span>{{ $move['name'] }}</span>
                            <span class="font-mono tabular-nums">{{ $this->money($move['amount']) }}</span>
                        </div>
                    @endforeach
                @endif

                <div class="flex items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <span>{{ __('point-of-sale::filament/pos/pages/terminal.closing.counted') }}</span>
                    <span class="font-mono tabular-nums">{{ $this->money((float) ($closingCash ?? 0)) }}</span>
                </div>

                <div @class(['flex items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400', 'font-semibold text-danger-600' => ! float_is_zero($this->cashDifference($cash), precisionDigits: 2)])>
                    <span>{{ __('point-of-sale::filament/pos/pages/terminal.closing.difference') }}</span>
                    <span class="font-mono tabular-nums">{{ $this->money($this->cashDifference($cash)) }}</span>
                </div>
            </div>
        @endif

        @foreach ($control['non_cash_payment_methods'] as $method)
            <div class="flex flex-col gap-1 border-b border-gray-100 pb-3 last-of-type:border-b-0 last-of-type:pb-0 dark:border-gray-800">
                <div class="flex items-center justify-between gap-4 text-base font-semibold text-gray-950 dark:text-gray-50">
                    <span>{{ $method['name'] }}</span>
                    <span class="font-mono tabular-nums">{{ $this->money($method['amount']) }}</span>
                </div>

                <div class="flex items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <span>{{ __('point-of-sale::filament/pos/pages/terminal.closing.counted') }}</span>

                    <div class="flex items-center gap-1">
                        <div class="relative w-40">
                            <x-filament::input.wrapper>
                                <x-filament::input
                                    type="number"
                                    step="0.01"
                                    class="pe-10"
                                    wire:model.live.debounce.500ms="paymentCounted.{{ $method['id'] }}"
                                />
                            </x-filament::input.wrapper>

                            <button
                                type="button"
                                class="absolute inset-y-0 end-3 my-auto flex size-6 items-center justify-center rounded text-gray-400 transition-colors hover:text-gray-600 dark:hover:text-gray-200"
                                wire:click="$set('paymentCounted.{{ $method['id'] }}', null)"
                                aria-label="{{ __('point-of-sale::filament/pos/pages/terminal.closing.clear') }}"
                            >
                                <x-filament::icon icon="heroicon-m-x-mark" class="size-5" />
                            </button>
                        </div>

                        <x-filament::icon-button
                            icon="heroicon-o-document-duplicate"
                            color="gray"
                            wire:click="copyExpectedPayment({{ $method['id'] }}, {{ $method['amount'] }})"
                            :label="__('point-of-sale::filament/pos/pages/terminal.closing.copy')"
                        />
                    </div>
                </div>

                <div @class(['flex items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400', 'font-semibold text-danger-600' => ! float_is_zero($this->paymentDifference($method), precisionDigits: 2)])>
                    <span>{{ __('point-of-sale::filament/pos/pages/terminal.closing.difference') }}</span>
                    <span class="font-mono tabular-nums">{{ $this->money($this->paymentDifference($method)) }}</span>
                </div>
            </div>
        @endforeach

        <div class="flex flex-col gap-2">
            <p class="text-sm font-semibold text-gray-950 dark:text-white">
                {{ __('point-of-sale::filament/pos/pages/terminal.closing.count') }}
            </p>

            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="number"
                            step="0.01"
                            class="pe-10"
                            wire:model.live.debounce.500ms="closingCash"
                        />
                    </x-filament::input.wrapper>

                    <button
                        type="button"
                        class="absolute inset-y-0 end-3 my-auto flex size-6 items-center justify-center rounded text-gray-400 transition-colors hover:text-gray-600 dark:hover:text-gray-200"
                        wire:click="$set('closingCash', null)"
                        aria-label="{{ __('point-of-sale::filament/pos/pages/terminal.closing.clear') }}"
                    >
                        <x-filament::icon icon="heroicon-m-x-mark" class="size-5" />
                    </button>
                </div>

                <x-filament::icon-button
                    icon="heroicon-o-banknotes"
                    color="gray"
                    size="lg"
                    wire:click="openMoneyDetails('closing')"
                    :label="__('point-of-sale::filament/pos/pages/terminal.money-details.label')"
                />

                <x-filament::icon-button
                    icon="heroicon-o-document-duplicate"
                    color="gray"
                    size="lg"
                    wire:click="copyExpectedCash"
                    :label="__('point-of-sale::filament/pos/pages/terminal.closing.copy')"
                />
            </div>

            @if ($control['amount_authorized_diff'] !== null)
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ __('point-of-sale::filament/pos/pages/terminal.closing.authorized-diff', ['amount' => $this->money($control['amount_authorized_diff'])]) }}
                </p>

                @if ($this->exceedsAuthorizedDifference($control))
                    <p class="text-xs font-semibold text-danger-600 dark:text-danger-400">
                        {{ __('point-of-sale::filament/pos/pages/terminal.closing.authorized-diff-exceeded') }}
                    </p>
                @endif
            @endif
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="flex flex-col gap-1">
                <p class="text-sm font-semibold text-gray-950 dark:text-white">
                    {{ __('point-of-sale::filament/pos/pages/terminal.closing.opening-note') }}
                </p>

                <p class="min-h-20 whitespace-pre-line rounded-lg border border-gray-200 px-3 py-2 text-[0.8125rem] text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $control['opening_notes'] ?: '—' }}</p>
            </div>

            <div class="flex flex-col gap-1">
                <p class="text-sm font-semibold text-gray-950 dark:text-white">
                    {{ __('point-of-sale::filament/pos/pages/terminal.closing.note') }}
                </p>

                <x-filament::input.wrapper>
                    <textarea
                        rows="3"
                        wire:model="closingNote"
                        class="block w-full border-none bg-transparent px-3 py-1.5 text-base text-gray-950 outline-none sm:text-sm dark:text-white"
                    >{{ $closingNote }}</textarea>
                </x-filament::input.wrapper>
            </div>
        </div>
    </div>

    <x-slot name="footerActions">
        <x-filament::button color="danger" wire:click="closeRegister">
            {{ __('point-of-sale::filament/pos/pages/terminal.closing.confirm') }}
        </x-filament::button>

        <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'pos-closing' })">
            {{ __('point-of-sale::filament/pos/pages/terminal.closing.discard') }}
        </x-filament::button>

        <x-filament::button color="gray" wire:click="openCashMovement('in')">
            {{ __('point-of-sale::filament/pos/pages/terminal.menu.cash-in-out') }}
        </x-filament::button>

        <x-filament::button color="gray" wire:click="downloadSalesDetails" icon="heroicon-m-arrow-down-tray">
            {{ __('point-of-sale::filament/pos/pages/terminal.closing.daily-sale') }}
        </x-filament::button>
    </x-slot>
</x-filament::modal>
