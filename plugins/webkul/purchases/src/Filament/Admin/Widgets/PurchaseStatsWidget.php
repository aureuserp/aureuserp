<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Purchase\Models\PurchaseOrder;

class PurchaseStatsWidget extends BaseWidget
{
    use HasWidgetShield;
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = '5s';

    public function getStats(): array
    {
        $totalValue = PurchaseOrder::whereIn('state', ['purchase', 'done'])->sum('total_amount');
        $totalOrders = PurchaseOrder::whereIn('state', ['purchase', 'done'])->count();
        $avgOrderValue = $totalOrders > 0 ? $totalValue / $totalOrders : 0;

        $pendingReceipts = PurchaseOrder::where('receipt_status', '!=', 'full')->whereIn('state', ['purchase', 'done'])->count();
        $pendingBills = PurchaseOrder::where('invoice_status', '!=', 'invoiced')->whereIn('state', ['purchase', 'done'])->count();
        $pendingApproval = PurchaseOrder::whereIn('state', ['draft', 'sent'])->count();

        $approvalColor = match (true) {
            $pendingApproval === 0 => 'success',
            $pendingApproval <= 3  => 'warning',
            default                => 'danger',
        };

        $pendingColor = ($pendingReceipts + $pendingBills) === 0 ? 'success' : 'warning';

        return [
            Stat::make('Total Purchase Overview', number_format($totalValue, 2))
                ->description("Orders: {$totalOrders} | Avg: ".number_format($avgOrderValue, 2))
                ->color('success')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('Pending Follow-up', $pendingReceipts + $pendingBills)
                ->description("Receipts: {$pendingReceipts} • Bills: {$pendingBills}")
                ->color($pendingColor)
                ->icon('heroicon-o-clock'),

            Stat::make('Pending Approvals', $pendingApproval)
                ->description('Quotations waiting for approval')
                ->color($approvalColor)
                ->icon('heroicon-o-exclamation-circle'),
        ];
    }
}
