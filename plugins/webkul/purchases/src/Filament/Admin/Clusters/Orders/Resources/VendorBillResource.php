<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\ParentResourceRegistration;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource as BaseBillResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorBillResource\Pages\EditBill;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorBillResource\Pages\ManagePayments;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorBillResource\Pages\ViewBill;
use Webkul\Purchase\Models\Bill;

class VendorBillResource extends BaseBillResource
{
    protected static ?string $model = Bill::class;

    protected static ?string $parentResource = VendorResource::class;

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
        return VendorResource::asParent()
            ->relationship('bills')
            ->inverseRelationship('partner');
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
