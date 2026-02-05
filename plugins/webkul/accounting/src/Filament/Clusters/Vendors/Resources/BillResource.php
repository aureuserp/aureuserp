<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources;

use Filament\Resources\Pages\Page;
use Webkul\Account\Filament\Resources\BillResource as BaseBillResource;
use Webkul\Accounting\Filament\Clusters\Vendors;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource\Pages\CreateBill;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource\Pages\EditBill;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource\Pages\ListBills;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource\Pages\ManagePayments;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource\Pages\ViewBill;
use Webkul\Accounting\Livewire\InvoiceSummary;
use Webkul\Accounting\Models\Bill;

class BillResource extends BaseBillResource
{
    protected static ?string $model = Bill::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static bool $shouldRegisterNavigation = true;

    protected static bool $isGloballySearchable = true;

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Vendors::class;

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function getModelLabel(): string
    {
        return __('accounting::filament/clusters/vendors/resources/bill.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/vendors/resources/bill.navigation.title');
    }

    public static function getSummaryComponent()
    {
        return InvoiceSummary::class;
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
            'index'    => ListBills::route('/'),
            'create'   => CreateBill::route('/create'),
            'edit'     => EditBill::route('/{record}/edit'),
            'view'     => ViewBill::route('/{record}'),
            'payments' => ManagePayments::route('/{record}/payments'),
        ];
    }
}
