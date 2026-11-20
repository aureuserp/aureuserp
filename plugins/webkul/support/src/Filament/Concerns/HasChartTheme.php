<?php

namespace Webkul\Support\Filament\Concerns;

use Filament\Support\Facades\FilamentColor;

trait HasChartTheme
{
    protected function brandColor(string $name, int $shade = 500, ?float $alpha = null): string
    {
        $value = FilamentColor::getColor($name)[$shade]
            ?? FilamentColor::getColor('gray')[$shade]
            ?? 'oklch(0.55 0 0)';

        if ($alpha !== null && str_ends_with($value, ')')) {
            return substr($value, 0, -1).' / '.$alpha.')';
        }

        return $value;
    }

    /**
     * @return list<string>
     */
    protected function chartRamp(): array
    {
        $colors = [];

        foreach (['info', 'success', 'warning', 'danger', 'primary', 'gray'] as $name) {
            $value = $this->brandColor($name);

            if (! in_array($value, $colors, true)) {
                $colors[] = $value;
            }
        }

        return $colors;
    }

    /**
     * A themed line dataset with a smooth area fill (brand colours act as fallbacks for the gradient plugin).
     *
     * @param  list<float>  $data
     * @return array<string, mixed>
     */
    protected function gradientLineDataset(array $data, ?string $label = null): array
    {
        return [
            'label'                 => $label ?? '',
            'data'                  => $data,
            'borderColor'           => $this->brandColor('primary'),
            'backgroundColor'       => $this->brandColor('primary', 500, 0.16),
            'fill'                  => true,
            'tension'               => 0.4,
            'borderWidth'           => 2,
            'pointRadius'           => 0,
            'pointHoverRadius'      => 6,
            'pointBackgroundColor'  => $this->brandColor('primary'),
            'pointHoverBorderColor' => $this->brandColor('gray', 50),
            'pointHoverBorderWidth' => 2,
        ];
    }

    /**
     * Vertical traffic-light gradient for a revenue line: high values read success, low values read danger.
     *
     * @return array<string, mixed>
     */
    protected function revenueLineGradient(): array
    {
        return [
            'borderDirection' => 'vertical',
            'border'          => [
                ['stop' => 0, 'color' => $this->brandColor('success')],
                ['stop' => 0.5, 'color' => $this->brandColor('warning')],
                ['stop' => 1, 'color' => $this->brandColor('danger')],
            ],
            'fill'            => [
                ['stop' => 0, 'color' => $this->brandColor('success', 500, 0.22)],
                ['stop' => 1, 'color' => $this->brandColor('success', 500, 0.0)],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function chartTooltip(): array
    {
        return [
            'backgroundColor' => $this->brandColor('gray', 900, 0.92),
            'titleColor'      => $this->brandColor('gray', 50),
            'bodyColor'       => $this->brandColor('gray', 200),
            'borderColor'     => $this->brandColor('gray', 700, 0.4),
            'borderWidth'     => 1,
            'padding'         => 12,
            'cornerRadius'    => 8,
            'boxPadding'      => 6,
            'displayColors'   => false,
            'usePointStyle'   => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function chartInteraction(string $mode = 'index', bool $intersect = false): array
    {
        return [
            'mode'      => $mode,
            'intersect' => $intersect,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function cartesianScales(int $maxTicks = 12, bool $yGrid = false): array
    {
        $ink = $this->brandColor('gray', 500);

        $axis = $this->brandColor('gray', 500, 0.35);

        $grid = $this->brandColor('gray', 500, 0.2);

        return [
            'x' => [
                'border' => ['display' => true, 'color' => $axis],
                'grid'   => ['display' => false],
                'ticks'  => ['color' => $ink, 'maxTicksLimit' => $maxTicks],
            ],
            'y' => [
                'beginAtZero' => true,
                'border'      => ['display' => true, 'color' => $axis],
                'grid'        => $yGrid ? ['color' => $grid, 'drawBorder' => false] : ['display' => false],
                'ticks'       => ['color' => $ink],
            ],
        ];
    }
}
