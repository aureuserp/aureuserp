@if ($this->needsOpeningControl())
    <div
        x-data
        x-init="$nextTick(() => $dispatch('open-modal', { id: 'pos-opening-control' }))"
    ></div>
@endif

<x-filament::modal
        id="pos-opening-control"
        width="md"
        :close-button="false"
        :close-by-clicking-away="false"
        :close-by-escaping="false"
    >
    <x-slot name="heading">
        {{ __('point-of-sale::filament/pos/pages/terminal.opening-control.heading') }}
    </x-slot>

    @if ($this->hasCashPaymentMethod())
        <div class="flex flex-col gap-1">
            <label class="text-sm font-medium text-gray-950 dark:text-white" for="opening-cash">
                {{ __('point-of-sale::filament/pos/pages/terminal.opening-control.cash') }}
            </label>

            @if ($previousClosingBalance !== null)
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('point-of-sale::filament/pos/pages/terminal.opening-control.previous-closing-balance', ['amount' => $this->money($previousClosingBalance)]) }}
                </p>
            @endif

            <div class="flex items-center gap-2">
                <x-filament::input.wrapper class="flex-1">
                    <x-filament::input
                        id="opening-cash"
                        type="number"
                        step="0.01"
                        wire:model="openingCash"
                    />
                </x-filament::input.wrapper>

                <x-filament::icon-button
                    icon="heroicon-o-banknotes"
                    color="gray"
                    size="lg"
                    wire:click="openMoneyDetails"
                    :label="__('point-of-sale::filament/pos/pages/terminal.money-details.label')"
                />
            </div>
        </div>
    @endif

    <div class="flex flex-col gap-1">
        <label class="text-sm font-medium text-gray-950 dark:text-white" for="opening-note">
            {{ __('point-of-sale::filament/pos/pages/terminal.opening-control.note') }}
        </label>

        <x-filament::input.wrapper>
            <textarea
                id="opening-note"
                rows="3"
                wire:model="openingNote"
                placeholder="{{ __('point-of-sale::filament/pos/pages/terminal.opening-control.placeholder') }}"
                class="block w-full border-none bg-transparent px-3 py-1.5 text-base text-gray-950 outline-none placeholder:text-gray-400 sm:text-sm dark:text-white dark:placeholder:text-gray-500"
            >{{ $openingNote }}</textarea>
        </x-filament::input.wrapper>
    </div>

    <x-slot name="footerActions">
        <x-filament::button wire:click="confirmOpening" size="lg">
            {{ __('point-of-sale::filament/pos/pages/terminal.opening-control.confirm') }}
        </x-filament::button>
    </x-slot>
</x-filament::modal>
