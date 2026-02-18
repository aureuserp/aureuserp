<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources;

use Filament\Resources\Pages\Page;
use Webkul\Account\Filament\Resources\RefundResource as BaseRefundResource;
use Webkul\Invoice\Filament\Clusters\Vendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource\Pages\CreateRefund;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource\Pages\EditRefund;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource\Pages\ListRefunds;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource\Pages\ManagePayments;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource\Pages\ViewRefund;
use Webkul\Invoice\Livewire\InvoiceSummary;
use Webkul\Invoice\Models\Refund;
use Webkul\Security\Traits\HasResourcePermissionQuery;

class RefundResource extends BaseRefundResource
{
    use HasResourcePermissionQuery;

    protected static ?string $model = Refund::class;

    protected static ?int $navigationSort = 2;

    protected static bool $shouldRegisterNavigation = true;

    protected static bool $isGloballySearchable = true;

    protected static ?string $cluster = Vendors::class;

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function getModelLabel(): string
    {
        return __('invoices::filament/clusters/vendors/resources/refund.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/vendors/resources/refund.navigation.title');
    }

    public static function getSummaryComponent()
    {
        return InvoiceSummary::class;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewRefund::class,
            EditRefund::class,
            ManagePayments::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'    => ListRefunds::route('/'),
            'create'   => CreateRefund::route('/create'),
            'edit'     => EditRefund::route('/{record}/edit'),
            'view'     => ViewRefund::route('/{record}'),
            'payments' => ManagePayments::route('/{record}/payments'),
        ];
    }
}
