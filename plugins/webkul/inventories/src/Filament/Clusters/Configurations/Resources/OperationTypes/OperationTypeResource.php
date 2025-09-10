<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypes;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypes\Pages\CreateOperationType;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypes\Pages\EditOperationType;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypes\Pages\ListOperationTypes;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypes\Pages\ViewOperationType;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypes\Schemas\OperationTypeForm;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypes\Schemas\OperationTypeInfolist;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypes\Tables\OperationTypesTable;
use Webkul\Inventory\Models\OperationType;

class OperationTypeResource extends Resource
{
    protected static ?string $model = OperationType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/operation-type.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/operation-type.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return OperationTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OperationTypesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OperationTypeInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListOperationTypes::route('/'),
            'create' => CreateOperationType::route('/create'),
            'view'   => ViewOperationType::route('/{record}'),
            'edit'   => EditOperationType::route('/{record}/edit'),
        ];
    }
}
