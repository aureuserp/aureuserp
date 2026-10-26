<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\ParentResourceRegistration;
use Webkul\Crm\Filament\Clusters\Sales;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationDeliveryResource\Pages\EditDelivery;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationDeliveryResource\Pages\ManageMoves;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationDeliveryResource\Pages\ViewDelivery;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationDeliveryResource as BaseQuotationDeliveryResource;

class QuotationDeliveryResource extends BaseQuotationDeliveryResource
{
    protected static ?string $parentResource = QuotationResource::class;

    protected static ?string $cluster = Sales::class;

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return QuotationResource::asParent()
            ->relationship('operations')
            ->inverseRelationship('saleOrder');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewDelivery::class,
            EditDelivery::class,
            ManageMoves::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'view'  => ViewDelivery::route('/{record}/view'),
            'edit'  => EditDelivery::route('/{record}/edit'),
            'moves' => ManageMoves::route('/{record}/moves'),
        ];
    }
}
