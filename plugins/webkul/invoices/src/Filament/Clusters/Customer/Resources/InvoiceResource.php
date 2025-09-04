<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources;

use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Webkul\Account\Filament\Resources\InvoiceResource as BaseInvoiceResource;
use Webkul\Invoice\Filament\Clusters\Customer;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\InvoiceResource\Pages;
use Webkul\Invoice\Models\Invoice;
use Webkul\Security\Traits\HasResourcePermissionQuery;

class InvoiceResource extends BaseInvoiceResource
{
    use HasResourcePermissionQuery;

    protected static ?string $model = Invoice::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Customer::class;

    protected static ?int $navigationSort = 1;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getModelLabel(): string
    {
        return __('invoices::filament/clusters/customers/resources/invoice.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/customers/resources/invoice.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'invoice_partner_display_name',
            'invoice_date',
            'invoice_date_due',
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewInvoice::class,
            Pages\EditInvoice::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view'   => Pages\ViewInvoice::route('/{record}'),
            'edit'   => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
