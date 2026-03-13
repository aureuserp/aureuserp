<?php

namespace Webkul\Invoice\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Invoice\Models\Invoice;

class InvoiceStatsWidget extends BaseWidget
{
    use HasWidgetShield;
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        $filters = $this->filters;

        $baseQuery = Invoice::query();

        if (!empty($filters['start_date'])) {
            $baseQuery->whereDate('invoice_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $baseQuery->whereDate('invoice_date', '<=', $filters['end_date']);
        }

        if (!empty($filters['salesperson_id'])) {
            $baseQuery->where('invoice_user_id', $filters['salesperson_id']);
        }

        if (!empty($filters['product_id'])) {
            $baseQuery->whereHas('lines', function ($q) use ($filters) {
                $q->where('display_type', 'product')
                    ->where('product_id', $filters['product_id']);
            });
        }

        $totalInvoiced = (clone $baseQuery)->sum('amount_total');
        $invoiceCount = (clone $baseQuery)->count();
        $unpaidAmount = (clone $baseQuery)->where('payment_state', 'not_paid')->sum('amount_total');
        $paidCount = (clone $baseQuery)->where('payment_state', 'paid')->count();
        $unpaidCount = (clone $baseQuery)->where('payment_state', 'not_paid')->count();

        $averageInvoice = $invoiceCount > 0 ? $totalInvoiced / $invoiceCount : 0;

   
        $colorForUnpaid = $unpaidAmount > 0 ? 'warning' : 'success';
        $unpaidRatio = $invoiceCount > 0 ? ($unpaidCount / $invoiceCount) : 0;

        $colorForUnpaidCount = match (true) {
            $unpaidRatio < 0.25 => 'success',
            $unpaidRatio < 0.5 => 'warning',
            default => 'danger',
        };

        $unpaidDescription = match (true) {
            $unpaidRatio < 0.25 => 'Healthy Credit',
            $unpaidRatio < 0.5 => 'Watch List',
            default => 'High Risk',
        };


        $colorForPaidCount = $paidCount > 0 ? 'success' : 'secondary';

        return [
            Stat::make('Total Invoiced', money(number_format($totalInvoiced, 2)))
                ->description('Unpaid Amount: ' . money(number_format($unpaidAmount, 2)))
                ->color($colorForUnpaid)
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('Average Invoice', money(number_format($averageInvoice, 2)))
                ->description("Total Invoices: {$invoiceCount}")
                ->color('info')
                ->icon('heroicon-o-chart-bar'),

            Stat::make('Paid Invoices', $paidCount)
                ->description($paidCount > 0 ? "All good!" : "No invoices paid yet")
                ->color($colorForPaidCount)
                ->icon('heroicon-o-check-circle'),

            Stat::make('Unpaid Invoices', $unpaidCount)
                ->description($unpaidDescription)
                ->color($colorForUnpaidCount)
                ->icon('heroicon-o-exclamation-circle'),

        ];
    }
}
