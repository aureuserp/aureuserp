<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource\Pages\CreateRule;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource\Pages\EditRule;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource\Pages\ListRules;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource\Pages\ViewRule;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource\Schemas\RuleForm;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource\Schemas\RuleInfolist;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource\Tables\RulesTable;
use Webkul\Inventory\Models\Rule;
use Webkul\Inventory\Settings\WarehouseSettings;

class RuleResource extends Resource
{
    protected static ?string $model = Rule::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return settings(WarehouseSettings::class)->enable_multi_steps_routes;
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/rule.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/rule.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return RuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RulesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RuleInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListRules::route('/'),
            'create' => CreateRule::route('/create'),
            'view'   => ViewRule::route('/{record}'),
            'edit'   => EditRule::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['sourceLocation' => function ($query) {
                $query->withTrashed();
            }])
            ->with(['destinationLocation' => function ($query) {
                $query->withTrashed();
            }])
            ->with(['route' => function ($query) {
                $query->withTrashed();
            }]);
    }
}
