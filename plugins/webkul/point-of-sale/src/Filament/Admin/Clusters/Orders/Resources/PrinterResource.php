<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources;

use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PrinterResource\Pages\CreatePrinter;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PrinterResource\Pages\EditPrinter;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PrinterResource\Pages\ListPrinters;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PrinterResource\Pages\ViewPrinter;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PrinterResource\Schemas\PrinterForm;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PrinterResource\Schemas\PrinterInfolist;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PrinterResource\Tables\PrintersTable;
use Webkul\PointOfSale\Models\Printer;

class PrinterResource extends Resource
{
    protected static ?string $model = Printer::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-printer';

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Orders::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('point-of-sale::models/printer.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('point-of-sale::models/printer.plural-title');
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/orders/resources/printer.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function form(Schema $schema): Schema
    {
        return PrinterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrintersTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PrinterInfolist::configure($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPrinter::class,
            EditPrinter::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPrinters::route('/'),
            'create' => CreatePrinter::route('/create'),
            'view'   => ViewPrinter::route('/{record}'),
            'edit'   => EditPrinter::route('/{record}/edit'),
        ];
    }
}
