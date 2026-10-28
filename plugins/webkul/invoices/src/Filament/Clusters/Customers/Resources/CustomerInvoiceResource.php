<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\ParentResourceRegistration;
use Webkul\Invoice\Filament\Clusters\Customers;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerInvoiceResource\Pages\EditInvoice;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerInvoiceResource\Pages\ManagePayments;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerInvoiceResource\Pages\ViewInvoice;
use Webkul\Invoice\Models\Invoice;

class CustomerInvoiceResource extends InvoiceResource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $parentResource = CustomerResource::class;

    protected static ?string $slug = 'invoices';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $cluster = Customers::class;

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
