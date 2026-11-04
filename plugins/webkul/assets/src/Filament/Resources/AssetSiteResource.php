<?php

namespace Webkul\Asset\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Asset\Filament\Clusters\Configurations;
use Webkul\Asset\Filament\Resources\AssetSiteResource\Pages\CreateAssetSite;
use Webkul\Asset\Filament\Resources\AssetSiteResource\Pages\EditAssetSite;
use Webkul\Asset\Filament\Resources\AssetSiteResource\Pages\ListAssetSites;
use Webkul\Asset\Filament\Resources\AssetSiteResource\Pages\ViewAssetSite;
use Webkul\Asset\Filament\Resources\AssetSiteResource\RelationManagers\SiteAssetsRelationManager;
use Webkul\Asset\Filament\Resources\AssetSiteResource\Schemas\AssetSiteForm;
use Webkul\Asset\Filament\Resources\AssetSiteResource\Schemas\AssetSiteInfolist;
use Webkul\Asset\Filament\Resources\AssetSiteResource\Tables\AssetSitesTable;
use Webkul\Asset\Models\AssetSite;

class AssetSiteResource extends Resource
{
    protected static ?string $model = AssetSite::class;

    protected static ?string $slug = 'sites';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationGroup(): string
    {
        return __('assets::filament/clusters/configurations.groups.warehouses');
    }

    public static function getModelLabel(): string
    {
        return __('assets::filament/resources/asset-site.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('assets::filament/resources/asset-site.plural-title');
    }

    public static function getNavigationLabel(): string
    {
        return __('assets::filament/resources/asset-site.navigation.title');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['siteType', 'defaultCustodian', 'company', 'creator']);
    }

    public static function form(Schema $schema): Schema
    {
        return AssetSiteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetSitesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AssetSiteInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            SiteAssetsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListAssetSites::route('/'),
            'create' => CreateAssetSite::route('/create'),
            'view'   => ViewAssetSite::route('/{record}'),
            'edit'   => EditAssetSite::route('/{record}/edit'),
        ];
    }
}
