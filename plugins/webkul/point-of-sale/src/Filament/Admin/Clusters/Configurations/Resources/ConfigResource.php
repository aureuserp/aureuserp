<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources;

use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource\Pages\CreateConfig;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource\Pages\EditConfig;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource\Pages\ListConfigs;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource\Pages\ViewConfig;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource\Schemas\ConfigForm;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource\Schemas\ConfigInfolist;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource\Tables\ConfigsTable;
use Webkul\PointOfSale\Models\Config;

class ConfigResource extends Resource
{
    protected static ?string $model = Config::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('point-of-sale::models/config.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('point-of-sale::models/config.plural-title');
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/configurations/resources/config.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'code'];
    }

    public static function form(Schema $schema): Schema
    {
        return ConfigForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConfigsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ConfigInfolist::configure($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewConfig::class,
            EditConfig::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListConfigs::route('/'),
            'create' => CreateConfig::route('/create'),
            'view'   => ViewConfig::route('/{record}'),
            'edit'   => EditConfig::route('/{record}/edit'),
        ];
    }
}
