<div>
    <x-filament::section>
        <x-slot name="heading">
            <x-filament::link :href="$this->getUrl()">
                {{ $operationType->name }}
            </x-filament::link>

            @if ($operationType->warehouse)
                <div class="text-sm font-normal text-gray-500 dark:text-gray-400">
                    {{ $operationType->warehouse->name }}
                </div>
            @endif
        </x-slot>

        <div class="flex items-start justify-between gap-4">
            <x-filament::button
                tag="a"
                :href="$this->getUrl('ready')"
                size="sm"
            >
                {{ $dashboard['ready_label'] }}
            </x-filament::button>

            <div class="flex flex-col gap-1 items-end">
                @foreach ($dashboard['links'] as $link)
                    <x-filament::link
                        :href="$link['url']"
                        size="sm"
                    >
                        {{ $link['count'] }} {{ $link['label'] }}
                    </x-filament::link>
                @endforeach
            </div>
        </div>

        <div class="mt-4" wire:ignore>
            <canvas
                id="operation-type-chart-{{ $operationType->id }}"
                style="height: 120px;"
            ></canvas>
        </div>
    </x-filament::section>

    @assets
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    @endassets

    @script
        <script>
            setTimeout(() => {
                const canvas = document.getElementById('operation-type-chart-{{ $operationType->id }}');

                if (! canvas || canvas.chart) {
                    return;
                }

                const chartData = @js($this->getChartData());

                canvas.chart = new Chart(canvas.getContext('2d'), {
                    type: 'bar',
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                        },
                        scales: {
                            x: { grid: { display: false } },
                            y: { beginAtZero: true, ticks: { precision: 0 } },
                        },
                    },
                });
            }, 100);
        </script>
    @endscript
</div>
