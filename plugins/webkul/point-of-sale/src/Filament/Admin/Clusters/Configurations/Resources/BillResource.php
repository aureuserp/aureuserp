<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\BillResource\Pages\ManageBills;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\BillResource\Schemas\BillForm;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\BillResource\Tables\BillsTable;
use Webkul\PointOfSale\Models\Bill;

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-rupee';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('point-of-sale::models/bill.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('point-of-sale::models/bill.plural-title');
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/configurations/resources/bill.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function form(Schema $schema): Schema
    {
        return BillForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BillsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBills::route('/'),
        ];
    }
}
