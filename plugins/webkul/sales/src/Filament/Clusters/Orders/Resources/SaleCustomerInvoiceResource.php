<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\ParentResourceRegistration;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource as BaseInvoiceResource;
use Webkul\Sale\Filament\Clusters\Orders;
use Webkul\Sale\Filament\Clusters\Orders\Resources\SaleCustomerInvoiceResource\Pages\EditInvoice;
use Webkul\Sale\Filament\Clusters\Orders\Resources\SaleCustomerInvoiceResource\Pages\ManagePayments;
use Webkul\Sale\Filament\Clusters\Orders\Resources\SaleCustomerInvoiceResource\Pages\ViewInvoice;
use Webkul\Sale\Models\Invoice;

class SaleCustomerInvoiceResource extends BaseInvoiceResource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $parentResource = CustomerResource::class;

    protected static ?string $slug = 'invoices';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $cluster = Orders::class;

    public static function canAccess(): bool
    {
        return static::$parentResource::canAccess();
    }

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return CustomerResource::asParent()
            ->relationship('invoices')
            ->inverseRelationship('partner');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewInvoice::class,
            EditInvoice::class,
            ManagePayments::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'view'     => ViewInvoice::route('/{record}/view'),
            'edit'     => EditInvoice::route('/{record}/edit'),
            'payments' => ManagePayments::route('/{record}/payments'),
        ];
    }
}
