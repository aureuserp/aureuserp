<?php

namespace Webkul\Invoice\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Invoice\Filament\Widgets\Concerns\HasInvoiceDashboardFilters;

class BillStatsWidget extends BaseWidget
{
    use HasInvoiceDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return __('invoices::filament/widgets/invoice-dashboard.bill-stats.heading');
    }

    protected function getStats(): array
    {
        $postedUrl = BillResource::getUrl('index', ['activeTableView' => 'posted']);

        $unpaidUrl = BillResource::getUrl('index', ['activeTableView' => 'unpaid']);

        return [
            $this->buildStat(
                __('invoices::filament/widgets/invoice-dashboard.bill-stats.total'),
                'heroicon-o-currency-dollar',
                'info',
                fn (?Carbon $start, ?Carbon $end): Builder => $this->scope($start, $end),
                money: true,
                abs: true,
            )->url($postedUrl),
            $this->buildStat(
                __('invoices::filament/widgets/invoice-dashboard.bill-stats.count'),
                'heroicon-o-document-text',
                'primary',
                fn (?Carbon $start, ?Carbon $end): Builder => $this->scope($start, $end),
            )->url($postedUrl),
            $this->buildStat(
                __('invoices::filament/widgets/invoice-dashboard.bill-stats.paid'),
                'heroicon-o-check-circle',
                'success',
                fn (?Carbon $start, ?Carbon $end): Builder => $this->stateScope('paid', $start, $end),
            )->url($postedUrl),
            $this->buildStat(
                __('invoices::filament/widgets/invoice-dashboard.bill-stats.unpaid'),
                'heroicon-o-exclamation-circle',
                'danger',
                fn (?Carbon $start, ?Carbon $end): Builder => $this->stateScope('not_paid', $start, $end),
            )->url($unpaidUrl),
        ];
    }

    protected function scope(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        return $this->vendorBills($start, $end);
    }

    protected function stateScope(string $state, ?Carbon $start = null, ?Carbon $end = null): Builder
    {
        $query = $this->scope($start, $end);

        return $query->where($query->qualifyColumn('payment_state'), $state);
    }
}
