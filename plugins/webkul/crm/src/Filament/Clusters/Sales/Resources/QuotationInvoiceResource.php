<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\ParentResourceRegistration;
use Webkul\Crm\Filament\Clusters\Sales;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationInvoiceResource\Pages\EditInvoice;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationInvoiceResource\Pages\ManagePayments;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationInvoiceResource\Pages\ViewInvoice;
use Webkul\Crm\Livewire\QuotationInvoiceSummary;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationInvoiceResource as BaseQuotationInvoiceResource;

class QuotationInvoiceResource extends BaseQuotationInvoiceResource
{
    protected static ?string $parentResource = QuotationResource::class;

    protected static ?string $cluster = Sales::class;

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return QuotationResource::asParent()
            ->relationship('invoices')
            ->inverseRelationship('salesOrders');
    }

    public static function getSummaryComponent()
    {
        return QuotationInvoiceSummary::class;
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
