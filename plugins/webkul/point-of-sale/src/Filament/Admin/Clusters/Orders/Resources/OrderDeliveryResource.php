<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\ParentResourceRegistration;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource as BaseDeliveryResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderDeliveryResource\Pages\EditDelivery;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderDeliveryResource\Pages\ManageMoves;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderDeliveryResource\Pages\ViewDelivery;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderDeliveryResource\Tables\OrderDeliveriesTable;
use Webkul\PointOfSale\Models\Delivery;

class OrderDeliveryResource extends BaseDeliveryResource
{
    protected static ?string $model = Delivery::class;

    protected static ?string $parentResource = OrderResource::class;

    protected static ?string $slug = 'deliveries';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $cluster = Orders::class;

    public static function canAccess(): bool
    {
        $parentResource = static::$parentResource;

        return $parentResource::canAccess();
    }

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return OrderResource::asParent()
            ->relationship('deliveries')
            ->inverseRelationship('posOrder');
    }

    public static function table(Table $table): Table
    {
        return OrderDeliveriesTable::configure($table, static::class);
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
