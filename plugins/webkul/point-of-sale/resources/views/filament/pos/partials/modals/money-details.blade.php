<x-filament::modal id="pos-money-details" width="xl">
    <x-slot name="heading">
        {{ __('point-of-sale::filament/pos/pages/terminal.money-details.label') }}
    </x-slot>

    <div class="grid gap-2 sm:grid-cols-2">
        @foreach ($this->getBills()->split(2) as $column)
            <div class="flex flex-col gap-2">
                @foreach ($column as $bill)
                    @php($key = $bill->id)

                    <div class="flex items-center gap-2">
                        <x-filament::icon-button
                            icon="heroicon-o-minus"
                            color="gray"
                            wire:click="stepMoneyDetail('{{ $key }}', -1)"
                            :label="__('point-of-sale::filament/pos/pages/terminal.money-details.decrease')"
                        />

                        <x-filament::input.wrapper class="w-20">
                            <x-filament::input
                                type="number"
                                min="0"
                                wire:model.live="moneyDetails.{{ $key }}"
                            />
                        </x-filament::input.wrapper>

                        <x-filament::icon-button
                            icon="heroicon-o-plus"
                            color="gray"
                            wire:click="stepMoneyDetail('{{ $key }}', 1)"
                            :label="__('point-of-sale::filament/pos/pages/terminal.money-details.increase')"
                        />

                        <span class="font-mono tabular-nums text-sm text-gray-950 dark:text-white">
                            {{ $this->money((float) $bill->value) }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <x-slot name="footerActions">
        <x-filament::button wire:click="confirmMoneyDetails" size="lg">
            {{ __('point-of-sale::filament/pos/pages/terminal.money-details.confirm') }}
        </x-filament::button>

        <p class="font-mono tabular-nums text-lg font-semibold text-gray-950 dark:text-white">
            {{ __('point-of-sale::filament/pos/pages/terminal.money-details.total', ['total' => $this->money($this->moneyDetailsTotal())]) }}
        </p>
    </x-slot>
</x-filament::modal>
