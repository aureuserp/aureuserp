<?php

namespace Webkul\Invoice\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\MoveType;
use Webkul\Invoice\Models\Invoice;

class RevenueOverTimeWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Revenue Over Time';

    protected ?string $maxHeight = '300px';

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
            $query->whereDate('invoice_date', '>=', $this->filters['start_date']);
        }

        if (! empty($this->filters['end_date'])) {
            $query->whereDate('invoice_date', '<=', $this->filters['end_date']);
        }

        if (! empty($this->filters['salesperson_id'])) {
            $query->whereIn('invoice_user_id', (array) $this->filters['salesperson_id']);
        }

        if (! empty($this->filters['product_id'])) {
            $query->whereHas('lines', function (Builder $q) {
                $q->where('display_type', 'product')
                    ->whereIn('product_id', (array) $this->filters['product_id']);
            });
        }

        if (! empty($this->filters['category_id'])) {
            $query->whereHas('lines.product', function (Builder $q) {
                $q->whereIn('category_id', (array) $this->filters['category_id']);
            });
        }

        if (! empty($this->filters['customer_id'])) {
            $query->whereIn('partner_id', (array) $this->filters['customer_id']);
        }

        if (! empty($this->filters['payment_state'])) {
            $query->whereIn('payment_state', (array) $this->filters['payment_state']);
        }

        $query->where('move_type', MoveType::OUT_INVOICE);

        $results = $query->selectRaw('DATE(invoice_date) as date, SUM(amount_total) as total')
            ->groupByRaw('DATE(invoice_date)')
            ->orderByRaw('DATE(invoice_date)')
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
