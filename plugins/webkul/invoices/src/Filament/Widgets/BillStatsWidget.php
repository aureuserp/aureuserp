<?php

namespace Webkul\Invoice\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Account\Enums\MoveType;
use Webkul\Invoice\Models\Invoice;

class BillStatsWidget extends BaseWidget
{
    use HasWidgetShield;
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $baseQuery = $this->getBaseFilteredQuery();
        $current = $this->applyDateRange(clone $baseQuery);
        $previous = $this->applyDateRange(clone $baseQuery, previous: true);

        $totalBills = (float) (clone $current)->sum('amount_total');
        $totalBillsPrevious = (float) (clone $previous)->sum('amount_total');

        $billCount = (clone $current)->count();
        $billCountPrevious = (clone $previous)->count();

        $paidBills = (clone $current)->where('payment_state', 'paid')->count();
        $paidBillsPrevious = (clone $previous)->where('payment_state', 'paid')->count();

        $unpaidBills = (clone $current)->where('payment_state', 'not_paid')->count();
        $unpaidBillsPrevious = (clone $previous)->where('payment_state', 'not_paid')->count();

        return [
            Stat::make('Total Bills Value', money($totalBills))
                ->description($this->calculateTrend($totalBills, $totalBillsPrevious)['description'])
                ->descriptionIcon($this->calculateTrend($totalBills, $totalBillsPrevious)['icon'])
                ->color($this->calculateTrend($totalBills, $totalBillsPrevious)['color'])
                ->icon('heroicon-o-currency-dollar')
                ->chart([$totalBillsPrevious, $totalBills]),

            Stat::make('Bills', number_format($billCount))
                ->description($this->calculateTrend($billCount, $billCountPrevious)['description'])
                ->descriptionIcon($this->calculateTrend($billCount, $billCountPrevious)['icon'])
                ->color($this->calculateTrend($billCount, $billCountPrevious)['color'])
                ->icon('heroicon-o-document-text')
                ->chart([$billCountPrevious, $billCount]),

            Stat::make('Paid Bills', $paidBills)
                ->description($this->calculateTrend($paidBills, $paidBillsPrevious)['description'])
                ->descriptionIcon($this->calculateTrend($paidBills, $paidBillsPrevious)['icon'])
                ->color($this->calculateTrend($paidBills, $paidBillsPrevious)['color'])
                ->icon('heroicon-o-check-circle')
                ->chart([$paidBillsPrevious, $paidBills]),

            Stat::make('Unpaid Bills', $unpaidBills)
                ->description($this->calculateTrend($unpaidBills, $unpaidBillsPrevious)['description'])
                ->descriptionIcon($this->calculateTrend($unpaidBills, $unpaidBillsPrevious)['icon'])
                ->color($this->calculateTrend($unpaidBills, $unpaidBillsPrevious)['color'])
                ->icon('heroicon-o-exclamation-circle')
                ->chart([$unpaidBillsPrevious, $unpaidBills]),
        ];
    }

    protected function getBaseFilteredQuery()
    {
        $filters = $this->filters ?? [];

        return Invoice::query()
            ->where('move_type', MoveType::IN_INVOICE)
            ->when(! empty($filters['salesperson_id']), fn ($query) => $query->whereIn('invoice_user_id', (array) $filters['salesperson_id']))
            ->when(! empty($filters['product_id']), fn ($query) => $query->whereHas('lines', fn ($lineQuery) => $lineQuery->where('display_type', 'product')->whereIn('product_id', (array) $filters['product_id'])))
            ->when(! empty($filters['category_id']), fn ($query) => $query->whereHas('lines.product', fn ($productQuery) => $productQuery->whereIn('category_id', (array) $filters['category_id'])))
            ->when(! empty($filters['vendor_id']), fn ($query) => $query->whereIn('partner_id', (array) $filters['vendor_id']))
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
