<?php

namespace Webkul\Project\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Locked;
use Livewire\Livewire;
use Webkul\Support\Filament\Concerns\HasChartTheme;

class TaskGraphChart extends ChartWidget
{
    use HasChartTheme;

    protected static bool $isLazy = false;

    protected static bool $isDiscovered = false;

    protected ?string $pollingInterval = null;

    #[Locked]
    public array $labels = [];

    #[Locked]
    public array $datasets = [];

    #[Locked]
    public string $graphType = 'bar';

    #[Locked]
    public bool $stacked = false;

    #[Locked]
    public ?string $chartMaxHeight = '24rem';

    public static function embedGraph(array $graph): Htmlable
    {
        $properties = [
            'labels'    => $graph['labels'] ?? [],
            'datasets'  => $graph['datasets'] ?? [],
            'graphType' => $graph['type'] ?? 'bar',
            'stacked'   => $graph['stacked'] ?? false,
        ];

        $html = Livewire::mount(static::class, $properties, 'projects-task-graph-'.md5(json_encode($properties)));

        return new HtmlString('<div class="projects-task-graph">'.$html.'</div>');
    }

    protected function getType(): string
    {
        return in_array($this->graphType, ['bar', 'line', 'pie'], true) ? $this->graphType : 'bar';
    }

    protected function getData(): array
    {
        if ($this->graphType === 'pie') {
            return $this->pieData();
        }

        $ramp = $this->chartRamp();

        $single = count($this->datasets) <= 1;

        $datasets = [];

        foreach ($this->datasets as $index => $set) {
            $colorName = $set['color'] ?? null;

            if ($this->graphType === 'line') {
                $line = $colorName ? $this->brandColor($colorName) : $this->brandColor('primary');

                $datasets[] = [
                    'label'                => $set['label'] ?? '',
                    'data'                 => $set['data'] ?? [],
                    'borderColor'          => $line,
                    'backgroundColor'      => $colorName ? $this->brandColor($colorName, 500, 0.16) : $this->brandColor('primary', 500, 0.16),
                    'fill'                 => $single,
                    'tension'              => 0.4,
                    'borderWidth'          => 2,
                    'pointRadius'          => 3,
                    'pointHoverRadius'     => 6,
                    'pointBackgroundColor' => $line,
                ];

                continue;
            }

            $colors = $single
                ? array_map(fn (int $i): string => $ramp[$i % count($ramp)], array_keys($set['data'] ?? []))
                : ($colorName ? $this->brandColor($colorName, 500, 0.85) : $ramp[$index % count($ramp)]);

            $dataset = [
                'label'                => $set['label'] ?? '',
                'data'                 => $set['data'] ?? [],
                'backgroundColor'      => $colors,
                'hoverBackgroundColor' => $colors,
                'borderRadius'         => 0,
                'borderSkipped'        => false,
                'maxBarThickness'      => 48,
            ];

            if ($this->stacked) {
                $dataset['stack'] = 'tasks';
            }

            $datasets[] = $dataset;
        }

        return [
            'labels'   => $this->labels,
            'datasets' => $datasets,
        ];
    }

    protected function pieData(): array
    {
        $ramp = $this->chartRamp();

        $totals = [];

        foreach (array_keys($this->labels) as $index) {
            $sum = 0;

            foreach ($this->datasets as $set) {
                $sum += $set['data'][$index] ?? 0;
            }

            $totals[] = $sum;
        }

        $colors = array_map(fn (int $i): string => $ramp[$i % count($ramp)], array_keys($totals));

        return [
            'labels'   => $this->labels,
            'datasets' => [[
                'data'            => $totals,
                'backgroundColor' => $colors,
                'borderWidth'     => 0,
            ]],
        ];
    }

    protected function getOptions(): array
    {
        $animation = ['duration' => 600, 'easing' => 'easeOutQuart'];

        $legendLabels = [
            'usePointStyle' => true,
            'boxWidth'      => 8,
            'boxHeight'     => 8,
            'color'         => $this->brandColor('gray', 500),
        ];

        if ($this->graphType === 'pie') {
            return [
                'plugins' => [
                    'legend'  => ['display' => true, 'position' => 'right', 'labels' => $legendLabels],
                    'tooltip' => $this->chartTooltip(),
                ],
                'animation'           => $animation,
                'maintainAspectRatio' => false,
            ];
        }

        $scales = $this->cartesianScales();

        if ($this->stacked) {
            $scales['x']['stacked'] = true;

            $scales['y']['stacked'] = true;
        }

        return [
            'plugins' => [
                'legend' => [
                    'display'  => count($this->datasets) > 1,
                    'position' => 'bottom',
                    'labels'   => $legendLabels,
                ],
                'tooltip' => $this->chartTooltip(),
            ],
            'animation'           => $animation,
            'interaction'         => $this->chartInteraction(),
            'scales'              => $scales,
            'maintainAspectRatio' => false,
        ];
    }

    protected function getMaxHeight(): ?string
    {
        return $this->chartMaxHeight;
    }
}
