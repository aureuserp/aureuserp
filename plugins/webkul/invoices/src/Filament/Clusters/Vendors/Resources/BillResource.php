<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources;

use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Webkul\Account\Filament\Resources\BillResource as BaseBillResource;
use Webkul\Invoice\Filament\Clusters\Vendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages;
use Webkul\Invoice\Models\Bill;
use Webkul\Security\Traits\HasResourcePermissionQuery;

class BillResource extends BaseBillResource
{
    use HasResourcePermissionQuery;

    protected static ?string $model = Bill::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Vendors::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function getModelLabel(): string
    {
        return __('invoices::filament/clusters/vendors/resources/bill.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/vendors/resources/bill.navigation.title');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewBill::class,
            Pages\EditBill::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBills::route('/'),
            'create' => Pages\CreateBill::route('/create'),
            'edit'   => Pages\EditBill::route('/{record}/edit'),
            'view'   => Pages\ViewBill::route('/{record}'),
        ];
    }
}
