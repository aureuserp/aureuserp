<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\ParentResourceRegistration;
use Webkul\Accounting\Filament\Clusters\Vendors;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorBillResource\Pages\EditBill;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorBillResource\Pages\ManagePayments;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorBillResource\Pages\ViewBill;
use Webkul\Accounting\Models\Bill;

class VendorBillResource extends BillResource
{
    protected static ?string $model = Bill::class;

    protected static ?string $parentResource = VendorResource::class;

    protected static ?string $slug = 'bills';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $cluster = Vendors::class;

    public static function canAccess(): bool
    {
        return static::$parentResource::canAccess();
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
