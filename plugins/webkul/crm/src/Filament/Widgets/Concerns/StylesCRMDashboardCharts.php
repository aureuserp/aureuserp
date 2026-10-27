<?php

namespace Webkul\Crm\Filament\Widgets\Concerns;

trait StylesCRMDashboardCharts
{
    protected const BAR_COLOR = '#2a78d6';

    protected const INK_MUTED = '#898781';

    protected const GRID = 'rgba(137, 135, 129, 0.25)';

    protected const PALETTE = ['#2a78d6', '#16a34a', '#f59e0b', '#dc2626', '#7c3aed', '#0891b2', '#db2777', '#65a30d'];

    protected function barDataset(string $label, array $data): array
    {
        return [
            'label'           => $label,
            'data'            => $data,
            'backgroundColor' => self::BAR_COLOR,
            'borderRadius'    => 4,
            'borderSkipped'   => 'bottom',
            'maxBarThickness' => 28,
        ];
    }

    protected function barOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins'             => ['legend' => ['display' => false]],
            'scales'              => [
                'x' => [
                    'border' => ['display' => false],
                    'grid'   => ['display' => false],
                    'ticks'  => ['color' => self::INK_MUTED],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'border'      => ['display' => false],
                    'grid'        => ['color' => self::GRID],
                    'ticks'       => ['color' => self::INK_MUTED],
                ],
            ],
        ];
    }
}
