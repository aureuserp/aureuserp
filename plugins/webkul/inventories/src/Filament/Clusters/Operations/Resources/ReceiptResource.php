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
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\CreateReceipt;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\EditReceipt;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\ListReceipts;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\ManageMoves;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\ViewReceipt;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Tables\ReceiptsTable;
use Webkul\Inventory\Models\Receipt;

class ReceiptResource extends Resource
{
    protected static ?string $model = Receipt::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = true;

    protected static ?string $cluster = Operations::class;

    public static function getModelLabel(): string
    {
        return __('inventories::filament/clusters/operations/resources/receipt.navigation.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/operations/resources/receipt.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/operations/resources/receipt.navigation.group');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'partner.name', 'origin'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('inventories::filament/clusters/operations/resources/receipt.global-search.partner') => $record->partner?->name ?? '—',
            __('inventories::filament/clusters/operations/resources/receipt.global-search.origin')  => $record->origin ?? '—',
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->whereHas('operationType', function (Builder $query) {
            $query->where('type', OperationType::INCOMING);
        });
    }

    public static function form(Schema $schema): Schema
    {
        return OperationResource::form($schema);
    }

    public static function table(Table $table): Table
    {
        return ReceiptsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OperationResource::infolist($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewReceipt::class,
            EditReceipt::class,
            ManageMoves::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListReceipts::route('/'),
            'create' => CreateReceipt::route('/create'),
            'edit'   => EditReceipt::route('/{record}/edit'),
            'view'   => ViewReceipt::route('/{record}/view'),
            'moves'  => ManageMoves::route('/{record}/moves'),
        ];
    }
}
