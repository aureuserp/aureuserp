<?php

namespace Webkul\Invoice\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Invoice\Models\Invoice;

class RevenueOverTimeWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Revenue Over Time';

    protected static bool $isLazy = false;

    protected function getType(): string
    {
        return 'line';
    }

    public function getColumnSpan(): int|string
    {
        return 'full';
    }

    protected function getData(): array
    {
        $query = Invoice::query()
            ->where('payment_state', 'paid');

        if (! empty($this->filters['start_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['start_date']);
        }

        if (! empty($this->filters['end_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['end_date']);
        }

        if (! empty($this->filters['salesperson_id'])) {
            $query->where('invoice_user_id', $this->filters['salesperson_id']);
        }

        if (! empty($this->filters['product_id'])) {
            $query->whereHas('lines', function (Builder $q) {
                $q->where('display_type', 'product')
                    ->where('product_id', $this->filters['product_id']);
            });
        }

        $results = $query->selectRaw('DATE(created_at) as date, SUM(amount_total) as total')
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at)')
            ->get();

        $labels = $results->pluck('date')->map(fn ($date) => date('M d', strtotime($date)))->toArray();
        $data = $results->pluck('total')->map(fn ($amount) => round((float) $amount, 2))->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Revenue',
                    'data'            => $data,
                    'backgroundColor' => '#3b82f6',
                ],
            ],
            'labels' => $labels,
        ];
    }
}
