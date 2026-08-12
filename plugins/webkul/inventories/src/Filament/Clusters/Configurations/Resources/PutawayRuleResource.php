<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PutawayRuleResource\Pages\ManagePutawayRules;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PutawayRuleResource\Schemas\PutawayRuleForm;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PutawayRuleResource\Tables\PutawayRulesTable;
use Webkul\Inventory\Models\PutawayRule;
use Webkul\Inventory\Settings\WarehouseSettings;

class PutawayRuleResource extends Resource
{
    protected static ?string $model = PutawayRule::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-pointing-in';

    protected static ?int $navigationSort = 5;

    protected static ?string $cluster = Configurations::class;

    protected static bool $isGloballySearchable = false;

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return settings(WarehouseSettings::class)->enable_locations;
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/putaway-rule.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/putaway-rule.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return PutawayRuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PutawayRulesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePutawayRules::route('/'),
        ];
    }
}
