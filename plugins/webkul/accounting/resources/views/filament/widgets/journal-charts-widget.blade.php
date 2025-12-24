<x-filament-widgets::widget>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($this->getJournals() as $journal)
            @livewire('accounting::journal-chart', [
                'journal' => $journal,
            ], key('journal-chart-'.$journal->id))
        @endforeach
    </div>
</x-filament-widgets::widget>
