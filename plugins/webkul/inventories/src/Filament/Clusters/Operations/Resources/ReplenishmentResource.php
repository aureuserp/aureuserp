<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Operations;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReplenishmentResource\Pages\ManageReplenishment;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReplenishmentResource\Schemas\ReplenishmentForm;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReplenishmentResource\Tables\ReplenishmentsTable;
use Webkul\Inventory\Models\OrderPoint;

class ReplenishmentResource extends Resource
{
    protected static ?string $model = OrderPoint::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-up-down';

    protected static ?int $navigationSort = 4;

    // TODO: Remove this when completed
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $cluster = Operations::class;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/operations/resources/replenishment.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/operations/resources/replenishment.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return ReplenishmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReplenishmentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ManageReplenishment::route('/'),
        ];
    }
}
