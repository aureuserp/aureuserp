<x-filament::section>
    <x-slot name="heading">
        @if ($journalUrl = $this->getUrl('index'))
            <x-filament::link
                tag="a"
                :href="$journalUrl"
            >
                {{ $journal->name }}
            </x-filament::link>
        @else
            {{ $journal->name }}
        @endif
    </x-slot>

    <x-slot name="afterHeader">
        <x-filament::button
            :href="$this->getUrl('create')"
            tag="a"
        >
            New
        </x-filament::button>
    </x-slot>

    <div class="flex flex-col gap-3 mb-4 items-end">
        @foreach ($dashboard['stats'] as $key => $stat)
            @if (($stat['value'] ?? 0) > 0 || ($stat['amount'] ?? null))
                <div class="flex gap-6 items-center">
                    <x-filament::link
                        tag="a"
                        href="{{ $stat['url'] ?? '#' }}"
                        class="inline-flex items-center"
                    >
                        @if (($stat['value'] ?? 0) > 0)
                            {{ $stat['value'] }}
                        @endif

                        {{ $stat['label'] ?? '' }}
                    </x-filament::link>

                    <span>
                        {{ $stat['formatted_amount'] }}
                    </span>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Chart --}}
    <div class="mt-4" style="height: 300px; position: relative;">
        <canvas 
            id="journal-chart-{{ $journal->id }}"
            wire:ignore
        ></canvas>
    </div>
</x-filament::section>

@assets
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
@endassets

@script
<script>
    setTimeout(() => {
        const ctx = document.getElementById('journal-chart-{{ $journal->id }}');
        if (ctx && !ctx.chart) {
            const chartData = @js($this->getChartData());
            
            ctx.chart = new Chart(ctx, {
                type: chartData.type,
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 2,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
    }, 100);
</script>
@endscript
