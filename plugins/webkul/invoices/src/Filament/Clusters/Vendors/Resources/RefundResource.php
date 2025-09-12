<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources;

use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Webkul\Account\Filament\Resources\RefundResource as BaseRefundResource;
use Webkul\Invoice\Filament\Clusters\Vendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource\Pages;
use Webkul\Invoice\Models\Refund;
use Webkul\Security\Traits\HasResourcePermissionQuery;

class RefundResource extends BaseRefundResource
{
    use HasResourcePermissionQuery;

    protected static ?string $model = Refund::class;

    protected static ?int $navigationSort = 2;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Vendors::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

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
