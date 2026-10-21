<x-filament::modal id="pos-cash-movement" width="md">
    <x-slot name="heading">
        {{ __('point-of-sale::filament/pos/pages/terminal.cash-movement.heading') }}
    </x-slot>

    <div class="grid grid-cols-2 gap-2">
        <button type="button" class="flex min-h-13 items-center justify-center rounded-lg border border-gray-200 bg-white text-[0.9375rem] font-medium text-gray-600 transition-colors aria-pressed:border-primary-600 aria-pressed:bg-primary-50 aria-pressed:font-semibold aria-pressed:text-primary-700 hover:bg-gray-50 dark:hover:bg-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:aria-pressed:border-primary-500 dark:aria-pressed:bg-primary-500/20 dark:aria-pressed:text-primary-300" aria-pressed="{{ $cashMovementType === 'in' ? 'true' : 'false' }}" wire:click="$set('cashMovementType', 'in')">
            {{ __('point-of-sale::filament/pos/pages/terminal.cash-movement.in') }}
        </button>

        <button type="button" class="flex min-h-13 items-center justify-center rounded-lg border border-gray-200 bg-white text-[0.9375rem] font-medium text-gray-600 transition-colors aria-pressed:border-primary-600 aria-pressed:bg-primary-50 aria-pressed:font-semibold aria-pressed:text-primary-700 hover:bg-gray-50 dark:hover:bg-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:aria-pressed:border-primary-500 dark:aria-pressed:bg-primary-500/20 dark:aria-pressed:text-primary-300" aria-pressed="{{ $cashMovementType === 'out' ? 'true' : 'false' }}" wire:click="$set('cashMovementType', 'out')">
            {{ __('point-of-sale::filament/pos/pages/terminal.cash-movement.out') }}
        </button>
    </div>

    <x-filament::input.wrapper>
        <x-filament::input type="number" step="0.01" wire:model="cashMovementAmount" :placeholder="__('point-of-sale::filament/pos/pages/terminal.cash-movement.amount')" />
    </x-filament::input.wrapper>

    <x-filament::input.wrapper>
        <x-filament::input type="text" wire:model="cashMovementReason" :placeholder="__('point-of-sale::filament/pos/pages/terminal.cash-movement.reason')" />
    </x-filament::input.wrapper>

    <x-filament::button size="lg" wire:click="recordCashMovement">
        {{ __('point-of-sale::filament/pos/pages/terminal.cash-movement.confirm') }}
    </x-filament::button>
</x-filament::modal>
