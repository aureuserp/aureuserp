<x-filament-widgets::widget>
    <div class="flex flex-col gap-y-6">
        <x-filament::tabs>
            <x-filament::tabs.item
                :active="$activeTab === 'all'"
                wire:click="$set('activeTab', 'all')"
            >
                {{ __('inventories::filament/widgets/operation-type-overview-widget.tabs.all') }}
            </x-filament::tabs.item>

            <x-filament::tabs.item
                :active="$activeTab === 'incoming'"
                wire:click="$set('activeTab', 'incoming')"
            >
                {{ __('inventories::filament/widgets/operation-type-overview-widget.tabs.receipts') }}
            </x-filament::tabs.item>

            <x-filament::tabs.item
                :active="$activeTab === 'outgoing'"
                wire:click="$set('activeTab', 'outgoing')"
            >
                {{ __('inventories::filament/widgets/operation-type-overview-widget.tabs.deliveries') }}
            </x-filament::tabs.item>

            <x-filament::tabs.item
                :active="$activeTab === 'internal'"
                wire:click="$set('activeTab', 'internal')"
            >
                {{ __('inventories::filament/widgets/operation-type-overview-widget.tabs.internal') }}
            </x-filament::tabs.item>
        </x-filament::tabs>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($this->getOperationTypes() as $operationType)
                @livewire(
                    'inventories-operation-type-card',
                    ['operationType' => $operationType],
                    key('operation-type-card-'.$operationType->id)
                )
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
