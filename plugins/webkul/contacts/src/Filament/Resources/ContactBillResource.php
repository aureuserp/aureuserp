<?php

namespace Webkul\Contact\Filament\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\ParentResourceRegistration;
use Webkul\Account\Filament\Resources\BillResource as BaseBillResource;
use Webkul\Account\Models\Bill;
use Webkul\Contact\Filament\Resources\ContactBillResource\Pages\EditBill;
use Webkul\Contact\Filament\Resources\ContactBillResource\Pages\ManagePayments;
use Webkul\Contact\Filament\Resources\ContactBillResource\Pages\ViewBill;

class ContactBillResource extends BaseBillResource
{
    protected static ?string $model = Bill::class;

    protected static ?string $parentResource = PartnerResource::class;

    protected static ?string $slug = 'bills';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        return static::$parentResource::canAccess();
    }

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return PartnerResource::asParent()
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
