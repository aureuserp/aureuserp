<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlans;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlans\Pages\EditActivityPlan;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlans\Pages\ListActivityPlans;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlans\Pages\ViewActivityPlan;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlans\RelationManagers\ActivityTemplateRelationManager;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlans\Schemas\ActivityPlanForm;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlans\Schemas\ActivityPlanInfolist;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlans\Tables\ActivityPlansTable;
use Webkul\Sale\Models\ActivityPlan;

class ActivityPlanResource extends Resource
{
    protected static ?string $model = ActivityPlan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $cluster = Configuration::class;

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/configurations/resources/activity-plan.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('sales::filament/clusters/configurations/resources/activity-plan.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return ActivityPlanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivityPlansTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ActivityPlanInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            ActivityTemplateRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListActivityPlans::route('/'),
            'view'   => ViewActivityPlan::route('/{record}'),
            'edit'   => EditActivityPlan::route('/{record}/edit'),
        ];
    }
}
