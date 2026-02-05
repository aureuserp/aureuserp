<x-filament-widgets::widget>
    <div class="flex flex-col gap-y-6">
        <x-filament::tabs>
            <x-filament::tabs.item
                :active="$activeTab === 'all'"
                wire:click="$set('activeTab', 'all')"
            >
                {{ __('accounting::filament/widgets/journal-charts-widget.tabs.all') }}
            </x-filament::tabs.item>

            <x-filament::tabs.item
                :active="$activeTab === 'sale'"
                wire:click="$set('activeTab', 'sale')"
            >
                {{ __('accounting::filament/widgets/journal-charts-widget.tabs.sales') }}
            </x-filament::tabs.item>

            <x-filament::tabs.item
                :active="$activeTab === 'purchase'"
                wire:click="$set('activeTab', 'purchase')"
            >
                {{ __('accounting::filament/widgets/journal-charts-widget.tabs.purchases') }}
            </x-filament::tabs.item>

            <x-filament::tabs.item
                :active="$activeTab === 'bank'"
                wire:click="$set('activeTab', 'bank')"
            >
                {{ __('accounting::filament/widgets/journal-charts-widget.tabs.bank') }}
            </x-filament::tabs.item>

            <x-filament::tabs.item
                :active="$activeTab === 'cash'"
                wire:click="$set('activeTab', 'cash')"
            >
                {{ __('accounting::filament/widgets/journal-charts-widget.tabs.cash') }}
            </x-filament::tabs.item>

            <x-filament::tabs.item
                :active="$activeTab === 'miscellaneous'"
                wire:click="$set('activeTab', 'miscellaneous')"
            >
                {{ __('accounting::filament/widgets/journal-charts-widget.tabs.miscellaneous') }}
            </x-filament::tabs.item>
        </x-filament::tabs>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($this->getJournals() as $journal)
                @livewire('accounting::journal-chart', [
                    'journal' => $journal,
                ], key('journal-chart-'.$journal->id))
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
