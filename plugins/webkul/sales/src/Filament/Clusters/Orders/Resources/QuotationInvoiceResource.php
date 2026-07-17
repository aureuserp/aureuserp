<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources;

use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Pages\Page;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource as BaseInvoiceResource;
use Webkul\Sale\Filament\Clusters\Orders;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationInvoiceResource\Pages\EditInvoice;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationInvoiceResource\Pages\ManagePayments;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationInvoiceResource\Pages\ViewInvoice;
use Webkul\Sale\Models\Invoice;

class QuotationInvoiceResource extends BaseInvoiceResource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $parentResource = QuotationResource::class;

    protected static ?string $slug = 'invoices';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $cluster = Orders::class;

    public static function canAccess(): bool
    {
        $parentResource = static::$parentResource;

        return $parentResource::canAccess();
    }

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return QuotationResource::asParent()
            ->relationship('invoices')
            ->inverseRelationship('salesOrders');
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
            'view' => ViewInvoice::route('/{record}/view'),
            'edit' => EditInvoice::route('/{record}/edit'),
            'payments' => ManagePayments::route('/{record}/payments'),
        ];
    }
}
