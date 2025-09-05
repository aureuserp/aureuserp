<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\Quantities;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Operations;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Quantities\Pages\ManageQuantities;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Quantities\Schemas\QuantityForm;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Quantities\Tables\QuantitiesTable;
use Webkul\Inventory\Models\ProductQuantity;

class QuantityResource extends Resource
{
    protected static ?string $model = ProductQuantity::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-up-down';

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Operations::class;

    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/operations/resources/quantity.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/operations/resources/quantity.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return QuantityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuantitiesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ManageQuantities::route('/'),
        ];
    }
}
