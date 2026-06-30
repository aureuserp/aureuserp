<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources;

use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Pages\Page;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource as BaseBillResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationBillResource\Pages\EditBill;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationBillResource\Pages\ManagePayments;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationBillResource\Pages\ViewBill;
use Webkul\Purchase\Models\Bill;

class QuotationBillResource extends BaseBillResource
{
    protected static ?string $model = Bill::class;

    protected static ?string $parentResource = QuotationResource::class;

    protected static ?string $slug = 'bills';

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
            'view' => ViewBill::route('/{record}/view'),
            'edit' => EditBill::route('/{record}/edit'),
            'payments' => ManagePayments::route('/{record}/payments'),
        ];
    }
}
