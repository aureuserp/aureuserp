<?php

namespace Webkul\Crm\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Locked;
use Livewire\Livewire;

class RecordsChart extends ChartWidget
{
    protected static bool $isLazy = false;

    protected static bool $isDiscovered = false;

    protected ?string $pollingInterval = null;

    #[Locked]
    public ?string $chartHeading = null;

    #[Locked]
    public array $chartData = [];

    #[Locked]
    public array $chartOptions = [];

    #[Locked]
    public ?string $chartMaxHeight = null;

    public static function embed(array $data, array $options = [], ?string $heading = null, ?string $maxHeight = null, ?string $class = null): Htmlable
    {
        $properties = [
            'chartData'      => $data,
            'chartOptions'   => $options,
            'chartHeading'   => $heading,
            'chartMaxHeight' => $maxHeight,
        ];

        $html = Livewire::mount(static::class, $properties, 'crm-records-chart-'.md5(json_encode($properties)));

        return new HtmlString($class ? '<div class="'.e($class).'">'.$html.'</div>' : $html);
    }

    public static function embedGraph(array $graph): Htmlable
    {
        return static::embed($graph['data'] ?? [], $graph['options'] ?? [], maxHeight: '24rem', class: 'crm-leads-graph');
    }

    public function getHeading(): string|Htmlable|null
    {
        return $this->chartHeading;
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        return $this->chartData;
    }

    protected function getOptions(): array
    {
        return $this->chartOptions;
    }

    protected function getMaxHeight(): ?string
    {
        return $this->chartMaxHeight;
    }
}
