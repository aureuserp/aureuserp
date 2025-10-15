<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources;

use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Webkul\Account\Filament\Resources\BillResource as BaseBillResource;
use Webkul\Invoice\Filament\Clusters\Vendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages\CreateBill;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages\EditBill;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages\ListBills;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages\ViewBill;
use Webkul\Invoice\Models\Bill;
use Webkul\Support\Filament\Forms\Components\Repeater;

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

    public static function getProductRepeater(): Repeater
    {
        return parent::getProductRepeater()
            ->extraItemActions([
                Action::make('openProduct')
                    ->tooltip('Open product')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn (array $arguments, Get $get): ?string => ProductResource::getUrl('edit', [
                        'record' => $get("products.{$arguments['item']}.product_id"),
                    ])
                    )
                    ->openUrlInNewTab()
                    ->visible(fn (array $arguments, Get $get): bool => filled($get("products.{$arguments['item']}.product_id"))
                    ),
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
