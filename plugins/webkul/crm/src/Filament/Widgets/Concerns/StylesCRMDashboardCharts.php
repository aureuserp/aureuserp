<?php

namespace Webkul\Crm\Filament\Widgets\Concerns;

trait StylesCRMDashboardCharts
{
    protected const BAR_COLOR = '#ef4444';

    protected const INK_MUTED = '#898781';

    protected const GRID = 'rgba(137, 135, 129, 0.25)';

    protected const PALETTE = ['#ef4444', '#3b82f6', '#22c55e', '#f59e0b', '#7c3aed', '#0891b2', '#db2777', '#65a30d'];

    protected function barDataset(string $label, array $data, string|array $color = self::BAR_COLOR): array
    {
        return [
            'label'           => $label,
            'data'            => $data,
            'backgroundColor' => $color,
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
