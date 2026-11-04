<?php

namespace Webkul\Asset\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Asset\Filament\Resources\AssetResource\Pages\CreateAsset;
use Webkul\Asset\Filament\Resources\AssetResource\Pages\EditAsset;
use Webkul\Asset\Filament\Resources\AssetResource\Pages\ListAssets;
use Webkul\Asset\Filament\Resources\AssetResource\Pages\ViewAsset;
use Webkul\Asset\Filament\Resources\AssetResource\RelationManagers\AssignmentsRelationManager;
use Webkul\Asset\Filament\Resources\AssetResource\Schemas\AssetForm;
use Webkul\Asset\Filament\Resources\AssetResource\Schemas\AssetInfolist;
use Webkul\Asset\Filament\Resources\AssetResource\Tables\AssetsTable;
use Webkul\Asset\Models\Asset;
use Webkul\Support\Enums\NavigationGroup;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $slug = 'assets/assets';

    protected static ?string $recordTitleAttribute = 'asset_tag';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::Assets;
    }

    public static function getModelLabel(): string
    {
        return __('assets::filament/resources/asset.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('assets::filament/resources/asset.plural-title');
    }

    public static function getNavigationLabel(): string
    {
        return __('assets::filament/resources/asset.navigation.title');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([
            'product.category',
            'warehouse.custodian',
            'currentEmployee',
            'currentSite.defaultCustodian',
            'company',
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return AssetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AssetInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            AssignmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListAssets::route('/'),
            'create' => CreateAsset::route('/create'),
            'view'   => ViewAsset::route('/{record}'),
            'edit'   => EditAsset::route('/{record}/edit'),
        ];
    }
}
