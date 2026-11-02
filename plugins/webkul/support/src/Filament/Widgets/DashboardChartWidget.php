<?php

namespace Webkul\Support\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Webkul\Support\Filament\Concerns\HasChartTheme;

abstract class DashboardChartWidget extends ChartWidget
{
    use HasChartTheme;

    protected static bool $isLazy = false;

    protected ?string $maxHeight = '300px';

    /**
     * Themed options for a vertical bar chart: staggered rise-in, axis baselines, no grid, themed tooltip.
     *
     * @return array<string, mixed>
     */
    protected function barChartOptions(int $maxTicks = 12, int $stagger = 70): array
    {
        return [
            'maintainAspectRatio' => false,
            'interaction'         => $this->chartInteraction('index'),
            'plugins'             => [
                'entrance' => ['type' => 'rising-bars', 'duration' => 700, 'stagger' => $stagger],

                'legend'  => ['display' => false],
                'tooltip' => $this->chartTooltip(),
            ],
            'scales'              => $this->cartesianScales($maxTicks),
        ];
    }

    /**
     * Themed options for a line chart: progressive left-to-right draw, axis baselines, themed tooltip.
     *
     * @return array<string, mixed>
     */
    protected function lineChartOptions(int $maxTicks = 12, int $stagger = 22): array
    {
        return [
            'maintainAspectRatio' => false,
            'interaction'         => $this->chartInteraction('index'),
            'plugins'             => [
                'entrance' => ['type' => 'progressive-line', 'duration' => 700, 'stagger' => $stagger],

                'legend'  => ['display' => false],
                'tooltip' => $this->chartTooltip(),
            ],
            'scales'              => $this->cartesianScales($maxTicks),
        ];
    }

    /**
     * Themed options for a doughnut chart: sweep-in, outer-radius hover grow, themed legend, percentage tooltip, optional center total.
     *
     * @return array<string, mixed>
     */
    protected function doughnutChartOptions(?string $centerText = null, ?string $centerLabel = null): array
    {
        $options = [
            'maintainAspectRatio' => false,
            'cutout'              => '62%',
            'layout'              => ['padding' => 26],
            'interaction'         => $this->chartInteraction('nearest', true),
            'plugins'             => [
                'entrance'  => ['type' => 'sweep', 'duration' => 900],

                'hoverGrow' => ['grow' => 20, 'speed' => 0.16],

                'legend'    => [
                    'position' => 'right',
                    'labels'   => [
                        'color'         => $this->brandColor('gray', 500),
                        'boxWidth'      => 12,
                        'boxHeight'     => 12,
                        'usePointStyle' => true,
                        'pointStyle'    => 'rectRounded',
                        'padding'       => 14,
                    ],
                ],

                'tooltip'   => array_merge($this->chartTooltip(), ['percentage' => true]),
            ],
        ];

        if ($centerText !== null) {
            $options['plugins']['centerText'] = [
                'text'       => $centerText,
                'label'      => $centerLabel ?? '',
                'labelColor' => $this->brandColor('gray', 500),
            ];
        }

        return $options;
    }
}
