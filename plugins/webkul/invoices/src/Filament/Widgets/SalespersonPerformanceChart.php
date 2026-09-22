<?php

namespace Webkul\Invoice\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Widgets\Concerns\HasInvoiceDashboardFilters;

class SalespersonPerformanceChart extends ChartWidget
{
    use HasInvoiceDashboardFilters, HasWidgetShield;

    protected const BAR_COLOR = '#2a78d6';

    protected const INK_MUTED = '#898781';

    protected const GRID = 'rgba(137, 135, 129, 0.25)';

    protected static ?int $sort = 5;

    protected static bool $isLazy = true;

    protected ?string $maxHeight = '300px';

    public function getHeading(): string|Htmlable|null
    {
        return __('invoices::filament/widgets/invoice-dashboard.salespersons.heading');
    }

    protected function getData(): array
    {
        $rows = $this->customerInvoices()
            ->leftJoin('users', 'users.id', '=', 'accounts_account_moves.invoice_user_id')
            ->groupBy('users.id', 'users.name')
            ->addSelect('users.name as name')
            ->selectRaw('SUM(accounts_account_moves.amount_total) as total_billed')
            ->havingRaw('SUM(accounts_account_moves.amount_total) > 0')
            ->orderByDesc('total_billed')
            ->limit(6)
            ->get();

        return [
            'datasets' => [
                [
                    'label'           => __('invoices::filament/widgets/invoice-dashboard.salespersons.total-billed'),
                    'data'            => $rows->map(fn ($row): float => round((float) $row->total_billed, 2))->all(),
                    'backgroundColor' => self::BAR_COLOR,
                    'borderRadius'    => 4,
                    'borderSkipped'   => 'bottom',
                    'maxBarThickness' => 40,
                ],
            ],
            'labels' => $rows
                ->map(fn ($row): string => $row->name ?: __('invoices::filament/widgets/invoice-dashboard.unknown'))
                ->all(),
        ];
    }

    protected function getOptions(): array
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

    protected function getType(): string
    {
        return 'bar';
    }
}
