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
    public array $values = [];

    #[Locked]
    public string $datasetLabel = '';

    #[Locked]
    public ?string $chartMaxHeight = '24rem';

    public static function embedGraph(array $graph): Htmlable
    {
        $properties = [
            'labels'       => $graph['labels'] ?? [],
            'values'       => $graph['data'] ?? [],
            'datasetLabel' => $graph['label'] ?? '',
        ];

        $html = Livewire::mount(static::class, $properties, 'projects-task-graph-'.md5(json_encode($properties)));

        return new HtmlString('<div class="projects-task-graph">'.$html.'</div>');
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $ramp = $this->chartRamp();

        $colors = array_map(
            fn (int $index): string => $ramp[$index % count($ramp)],
            array_keys($this->values),
        );

        return [
            'labels'   => $this->labels,
            'datasets' => [[
                'label'                => $this->datasetLabel,
                'data'                 => $this->values,
                'backgroundColor'      => $colors,
                'hoverBackgroundColor' => $colors,
                'borderRadius'         => 8,
                'borderSkipped'        => false,
                'maxBarThickness'      => 44,
            ]],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend'  => ['display' => false],
                'tooltip' => $this->chartTooltip(),
            ],
            'interaction'         => $this->chartInteraction(),
            'scales'              => $this->cartesianScales(),
            'maintainAspectRatio' => false,
        ];
    }

    protected function getMaxHeight(): ?string
    {
        return $this->chartMaxHeight;
    }
}
