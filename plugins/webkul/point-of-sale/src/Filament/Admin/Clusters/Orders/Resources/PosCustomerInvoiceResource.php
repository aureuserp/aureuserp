<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\ParentResourceRegistration;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource as BaseInvoiceResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PosCustomerInvoiceResource\Pages\EditInvoice;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PosCustomerInvoiceResource\Pages\ManagePayments;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PosCustomerInvoiceResource\Pages\ViewInvoice;
use Webkul\PointOfSale\Models\Invoice;

class PosCustomerInvoiceResource extends BaseInvoiceResource
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
