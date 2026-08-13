<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\ParentResourceRegistration;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderDeliveryResource\Pages\EditDelivery;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderDeliveryResource\Pages\ManageMoves;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderDeliveryResource\Pages\ViewDelivery;

class OrderDeliveryResource extends QuotationDeliveryResource
{
    protected static ?string $parentResource = OrderResource::class;

    protected static ?string $slug = 'deliveries';

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return OrderResource::asParent()
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
