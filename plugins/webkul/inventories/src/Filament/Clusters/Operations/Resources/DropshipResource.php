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
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource\Pages\CreateDropship;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource\Pages\EditDropship;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource\Pages\ListDropships;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource\Pages\ManageMoves;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource\Pages\ViewDropship;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource\Tables\DropshipsTable;
use Webkul\Inventory\Models\Dropship;
use Webkul\Inventory\Settings\LogisticSettings;

class DropshipResource extends Resource
{
    protected static ?string $model = Dropship::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $cluster = Operations::class;

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return settings(LogisticSettings::class)->enable_dropshipping;
    }

    public static function getModelLabel(): string
    {
        return __('inventories::filament/clusters/operations/resources/dropship.navigation.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/operations/resources/dropship.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/operations/resources/dropship.navigation.group');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'partner.name', 'origin'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('inventories::filament/clusters/operations/resources/dropship.global-search.partner') => $record->partner?->name ?? '—',
            __('inventories::filament/clusters/operations/resources/dropship.global-search.origin')  => $record->origin ?? '—',
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->whereHas('operationType', function (Builder $query) {
            $query->where('type', OperationType::DROPSHIP);
        });
    }

    public static function form(Schema $schema): Schema
    {
        return OperationResource::form($schema);
    }

    public static function table(Table $table): Table
    {
        return DropshipsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OperationResource::infolist($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewDropship::class,
            EditDropship::class,
            ManageMoves::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListDropships::route('/'),
            'create' => CreateDropship::route('/create'),
            'edit'   => EditDropship::route('/{record}/edit'),
            'view'   => ViewDropship::route('/{record}/view'),
            'moves'  => ManageMoves::route('/{record}/moves'),
        ];
    }
}
