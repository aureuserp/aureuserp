<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\ParentResourceRegistration;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderBillResource\Pages\EditBill;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderBillResource\Pages\ManagePayments;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderBillResource\Pages\ViewBill;

class PurchaseOrderBillResource extends QuotationBillResource
{
    protected static ?string $parentResource = PurchaseOrderResource::class;

    protected static ?string $slug = 'bills';

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return PurchaseOrderResource::asParent()
            ->relationship('bills')
            ->inverseRelationship('purchaseOrders');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewBill::class,
            EditBill::class,
            ManagePayments::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'view'     => ViewBill::route('/{record}/view'),
            'edit'     => EditBill::route('/{record}/edit'),
            'payments' => ManagePayments::route('/{record}/payments'),
        ];
    }
}
