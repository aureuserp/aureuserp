<?php

namespace Webkul\Invoice\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Account\Enums\MoveType;
use Webkul\Invoice\Models\Invoice;

class InvoiceStatsWidget extends BaseWidget
{
    use HasWidgetShield;
    use InteractsWithPageFilters;

    // protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        $baseQuery = $this->getBaseFilteredQuery();
        $current = $this->applyDateRange(clone $baseQuery);
        $previous = $this->applyDateRange(clone $baseQuery, previous: true);

        $totalInvoiced = (float) (clone $current)->sum('amount_total');
        $totalInvoicedPrevious = (float) (clone $previous)->sum('amount_total');

        $invoiceCount = (clone $current)->count();
        $invoiceCountPrevious = (clone $previous)->count();

        $paidCount = (clone $current)->where('payment_state', 'paid')->count();
        $paidCountPrevious = (clone $previous)->where('payment_state', 'paid')->count();

        $unpaidCount = (clone $current)->where('payment_state', 'not_paid')->count();
        $unpaidCountPrevious = (clone $previous)->where('payment_state', 'not_paid')->count();

        return [
            Stat::make('Total Invoiced', money($totalInvoiced))
                ->description($this->calculateTrend($totalInvoiced, $totalInvoicedPrevious)['description'])
                ->descriptionIcon($this->calculateTrend($totalInvoiced, $totalInvoicedPrevious)['icon'])
                ->color($this->calculateTrend($totalInvoiced, $totalInvoicedPrevious)['color'])
                ->icon('heroicon-o-currency-dollar')
                ->chart([$totalInvoicedPrevious, $totalInvoiced]),

            Stat::make('Invoices', number_format($invoiceCount))
                ->description($this->calculateTrend($invoiceCount, $invoiceCountPrevious)['description'])
                ->descriptionIcon($this->calculateTrend($invoiceCount, $invoiceCountPrevious)['icon'])
                ->color($this->calculateTrend($invoiceCount, $invoiceCountPrevious)['color'])
                ->icon('heroicon-o-document-text')
                ->chart([$invoiceCountPrevious, $invoiceCount]),

            Stat::make('Paid Invoices', $paidCount)
                ->description($this->calculateTrend($paidCount, $paidCountPrevious)['description'])
                ->descriptionIcon($this->calculateTrend($paidCount, $paidCountPrevious)['icon'])
                ->color($this->calculateTrend($paidCount, $paidCountPrevious)['color'])
                ->icon('heroicon-o-check-circle')
                ->chart([$paidCountPrevious, $paidCount]),

            Stat::make('Unpaid Invoices', $unpaidCount)
                ->description($this->calculateTrend($unpaidCount, $unpaidCountPrevious)['description'])
                ->descriptionIcon($this->calculateTrend($unpaidCount, $unpaidCountPrevious)['icon'])
                ->color($this->calculateTrend($unpaidCount, $unpaidCountPrevious)['color'])
                ->icon('heroicon-o-exclamation-circle')
                ->chart([$unpaidCountPrevious, $unpaidCount]),
        ];
    }

    protected function getBaseFilteredQuery()
    {
        $filters = $this->filters ?? [];

        return Invoice::query()
            ->where('move_type', MoveType::OUT_INVOICE)
            ->when(! empty($filters['salesperson_id']), fn ($query) => $query->whereIn('invoice_user_id', (array) $filters['salesperson_id']))
            ->when(! empty($filters['product_id']), fn ($query) => $query->whereHas('lines', fn ($lineQuery) => $lineQuery->where('display_type', 'product')->whereIn('product_id', (array) $filters['product_id'])))
            ->when(! empty($filters['category_id']), fn ($query) => $query->whereHas('lines.product', fn ($productQuery) => $productQuery->whereIn('category_id', (array) $filters['category_id'])))
            ->when(! empty($filters['customer_id']), fn ($query) => $query->whereIn('partner_id', (array) $filters['customer_id']))
            ->when(! empty($filters['payment_state']), fn ($query) => $query->whereIn('payment_state', (array) $filters['payment_state']));
    }

    protected function applyDateRange($query, bool $previous = false)
    {
        $filters = $this->filters ?? [];
        $start = ! empty($filters['start_date']) ? Carbon::parse($filters['start_date']) : now()->subMonth();
        $end = ! empty($filters['end_date']) ? Carbon::parse($filters['end_date']) : now();

        if ($previous) {
            $periodLength = $end->diffInDays($start) + 1;
            $previousEnd = $start->copy()->subDay();
            $previousStart = $previousEnd->copy()->subDays($periodLength - 1);

            return $query->whereBetween('invoice_date', [$previousStart->startOfDay(), $previousEnd->endOfDay()]);
        }

        return $query->whereBetween('invoice_date', [$start->startOfDay(), $end->endOfDay()]);
    }

    protected function calculateTrend(float|int $current, float|int $previous): array
    {
        if ($previous == 0 && $current == 0) {
            return ['description' => 'No change', 'icon' => 'heroicon-m-minus', 'color' => 'gray'];
        }

        if ($previous == 0 && $current > 0) {
            return ['description' => '100% increase', 'icon' => 'heroicon-m-arrow-trending-up', 'color' => 'success'];
        }

        $percentage = round((($current - $previous) / $previous) * 100, 1);

        if ($percentage > 0) {
            return ['description' => abs($percentage).'% increase', 'icon' => 'heroicon-m-arrow-trending-up', 'color' => 'success'];
        }

        if ($percentage < 0) {
            return ['description' => abs($percentage).'% decrease', 'icon' => 'heroicon-m-arrow-trending-down', 'color' => 'danger'];
        }

        return ['description' => 'No change', 'icon' => 'heroicon-m-minus', 'color' => 'gray'];
    }
}
