<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\ParentResourceRegistration;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderInvoiceResource\Pages\EditInvoice;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderInvoiceResource\Pages\ManagePayments;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderInvoiceResource\Pages\ViewInvoice;

class OrderInvoiceResource extends QuotationInvoiceResource
{
    protected static ?string $slug = 'invoices';

    protected static ?string $parentResource = OrderResource::class;

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return OrderResource::asParent()
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
            'view'     => ViewInvoice::route('/{record}/view'),
            'edit'     => EditInvoice::route('/{record}/edit'),
            'payments' => ManagePayments::route('/{record}/payments'),
        ];
    }
}
