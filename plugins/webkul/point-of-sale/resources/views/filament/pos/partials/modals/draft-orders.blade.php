<x-filament::modal id="pos-draft-orders" width="md">
    <x-slot name="heading">
        {{ __('point-of-sale::filament/pos/pages/terminal.draft-orders.heading') }}
    </x-slot>

    <p class="text-sm text-gray-500 dark:text-gray-400">
        {{ __('point-of-sale::filament/pos/pages/terminal.draft-orders.message') }}
    </p>

    <x-slot name="footerActions">
        <x-filament::button tag="a" :href="$this->ordersUrl()" color="primary">
            {{ __('point-of-sale::filament/pos/pages/terminal.draft-orders.review-orders') }}
        </x-filament::button>

        <x-filament::button color="danger" wire:click="cancelDraftOrders">
            {{ __('point-of-sale::filament/pos/pages/terminal.draft-orders.cancel-orders') }}
        </x-filament::button>
    </x-slot>
</x-filament::modal>
