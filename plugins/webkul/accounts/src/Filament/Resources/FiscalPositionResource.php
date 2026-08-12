<?php

namespace Webkul\Account\Filament\Resources;

use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\FiscalPositionResource\Pages\CreateFiscalPosition;
use Webkul\Account\Filament\Resources\FiscalPositionResource\Pages\EditFiscalPosition;
use Webkul\Account\Filament\Resources\FiscalPositionResource\Pages\ListFiscalPositions;
use Webkul\Account\Filament\Resources\FiscalPositionResource\Pages\ManageFiscalPositionTax;
use Webkul\Account\Filament\Resources\FiscalPositionResource\Pages\ViewFiscalPosition;
use Webkul\Account\Filament\Resources\FiscalPositionResource\Schemas\FiscalPositionForm;
use Webkul\Account\Filament\Resources\FiscalPositionResource\Schemas\FiscalPositionInfolist;
use Webkul\Account\Filament\Resources\FiscalPositionResource\Tables\FiscalPositionsTable;
use Webkul\Account\Models\FiscalPosition;

class FiscalPositionResource extends Resource
{
    protected static ?string $model = FiscalPosition::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-uturn-left';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return FiscalPositionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FiscalPositionsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FiscalPositionInfolist::configure($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewFiscalPosition::class,
            EditFiscalPosition::class,
            ManageFiscalPositionTax::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'               => ListFiscalPositions::route('/'),
            'create'              => CreateFiscalPosition::route('/create'),
            'view'                => ViewFiscalPosition::route('/{record}'),
            'edit'                => EditFiscalPosition::route('/{record}/edit'),
            'fiscal-position-tax' => ManageFiscalPositionTax::route('/{record}/fiscal-position-tax'),
        ];
    }
}
