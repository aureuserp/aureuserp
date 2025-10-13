<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources;

use Filament\Resources\Pages\Page;
use Webkul\Account\Filament\Resources\BillResource as BaseBillResource;
use Webkul\Invoice\Filament\Clusters\Vendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages\CreateBill;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages\EditBill;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages\ListBills;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages\ViewBill;
use Webkul\Invoice\Models\Bill;

class BillResource extends BaseBillResource
{
    protected static ?string $model = Bill::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Vendors::class;

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
            ViewBill::class,
            EditBill::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListBills::route('/'),
            'create' => CreateBill::route('/create'),
            'edit'   => EditBill::route('/{record}/edit'),
            'view'   => ViewBill::route('/{record}'),
        ];
    }
}
