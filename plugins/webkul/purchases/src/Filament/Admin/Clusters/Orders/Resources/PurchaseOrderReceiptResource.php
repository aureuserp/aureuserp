<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\ParentResourceRegistration;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderReceiptResource\Pages\EditReceipt;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderReceiptResource\Pages\ManageMoves;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderReceiptResource\Pages\ViewReceipt;

class PurchaseOrderReceiptResource extends QuotationReceiptResource
{
    protected static ?string $parentResource = PurchaseOrderResource::class;

    protected static ?string $slug = 'receipts';

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return PurchaseOrderResource::asParent()
            ->relationship('operations')
            ->inverseRelationship('purchaseOrders');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewReceipt::class,
            EditReceipt::class,
            ManageMoves::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'view'  => ViewReceipt::route('/{record}/view'),
            'edit'  => EditReceipt::route('/{record}/edit'),
            'moves' => ManageMoves::route('/{record}/moves'),
        ];
    }
}
