<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\ParentResourceRegistration;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource as BaseDeliveryResource;
use Webkul\Inventory\Models\Delivery;
use Webkul\Sale\Filament\Clusters\Orders;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationDeliveryResource\Pages\EditDelivery;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationDeliveryResource\Pages\ManageMoves;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationDeliveryResource\Pages\ViewDelivery;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationDeliveryResource\Tables\QuotationDeliveriesTable;

class QuotationDeliveryResource extends BaseDeliveryResource
{
    protected static ?string $model = Delivery::class;

    protected static ?string $parentResource = QuotationResource::class;

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
        return QuotationResource::asParent()
            ->relationship('operations')
            ->inverseRelationship('saleOrder');
    }

    public static function table(Table $table): Table
    {
        return QuotationDeliveriesTable::configure($table, static::class);
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
