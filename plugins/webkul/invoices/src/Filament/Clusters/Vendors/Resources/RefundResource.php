<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources;

use Filament\Resources\Pages\Page;
use Webkul\Account\Filament\Resources\RefundResource as BaseRefundResource;
use Webkul\Invoice\Filament\Clusters\Vendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource\Pages;
use Webkul\Invoice\Models\Move;

class RefundResource extends BaseRefundResource
{
    protected static ?string $model = Move::class;

    protected static ?int $navigationSort = 2;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Vendors::class;

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function getModelLabel(): string
    {
        return __('invoices::filament/clusters/vendors/resources/refund.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/vendors/resources/refund.navigation.title');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewRefund::class,
            Pages\EditRefund::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRefunds::route('/'),
            'create' => Pages\CreateRefund::route('/create'),
            'edit'   => Pages\EditRefund::route('/{record}/edit'),
            'view'   => Pages\ViewRefund::route('/{record}'),
        ];
    }
}
