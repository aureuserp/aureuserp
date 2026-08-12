<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources;

use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Inventory\Enums\OperationType;
use Webkul\Inventory\Filament\Clusters\Operations;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource\Pages\CreateDelivery;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource\Pages\EditDelivery;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource\Pages\ListDeliveries;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource\Pages\ManageMoves;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource\Pages\ViewDelivery;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource\Tables\DeliveriesTable;
use Webkul\Inventory\Models\Delivery;

class DeliveryResource extends Resource
{
    protected static ?string $model = Delivery::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = true;

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Operations::class;

    public static function getModelLabel(): string
    {
        return __('inventories::filament/clusters/operations/resources/delivery.navigation.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/operations/resources/delivery.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/operations/resources/delivery.navigation.group');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'partner.name', 'origin'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('inventories::filament/clusters/operations/resources/delivery.global-search.partner') => $record->partner?->name ?? '—',
            __('inventories::filament/clusters/operations/resources/delivery.global-search.origin')  => $record->origin ?? '—',
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->whereHas('operationType', function (Builder $query) {
            $query->where('type', OperationType::OUTGOING);
        });
    }

    public static function form(Schema $schema): Schema
    {
        return OperationResource::form($schema);
    }

    public static function table(Table $table): Table
    {
        return DeliveriesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OperationResource::infolist($schema);
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
            'index'  => ListDeliveries::route('/'),
            'create' => CreateDelivery::route('/create'),
            'view'   => ViewDelivery::route('/{record}/view'),
            'edit'   => EditDelivery::route('/{record}/edit'),
            'moves'  => ManageMoves::route('/{record}/moves'),
        ];
    }
}
