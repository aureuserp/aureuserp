<?php

namespace Webkul\PluginManager\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Lang;
use Webkul\PluginManager\Filament\Resources\PluginResource\Pages\ListPlugins;
use Webkul\PluginManager\Filament\Resources\PluginResource\Schemas\PluginInfolist;
use Webkul\PluginManager\Filament\Resources\PluginResource\Tables\PluginsTable;
use Webkul\PluginManager\Models\Plugin;
use Webkul\Support\Enums\NavigationGroup;

class PluginResource extends Resource
{
    protected static ?string $model = Plugin::class;

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Plugin;
    }

    public static function getModelLabel(): string
    {
        return __('plugin-manager::filament/resources/plugin.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('plugin-manager::filament/resources/plugin.title');
    }

    public static function table(Table $table): Table
    {
        return PluginsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PluginInfolist::configure($schema);
    }

    public static function localize(string $group, string $name, ?string $fallback = null): string
    {
        $key = "plugin-manager::filament/resources/plugin.{$group}.{$name}";

        return Lang::has($key) ? __($key) : ($fallback ?? $name);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlugins::route('/'),
        ];
    }
}
