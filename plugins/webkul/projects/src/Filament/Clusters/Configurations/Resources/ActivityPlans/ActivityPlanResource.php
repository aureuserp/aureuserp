<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlans;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Project\Filament\Clusters\Configurations;
use Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlans\Pages\EditActivityPlan;
use Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlans\Pages\ListActivityPlans;
use Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlans\Pages\ViewActivityPlan;
use Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlans\RelationManagers\ActivityTemplateRelationManager;
use Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlans\Schemas\ActivityPlanForm;
use Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlans\Schemas\ActivityPlanInfolist;
use Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlans\Tables\ActivityPlansTable;
use Webkul\Project\Models\ActivityPlan;

class ActivityPlanResource extends Resource
{
    protected static ?string $model = ActivityPlan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?int $navigationSort = 5;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/clusters/configurations/resources/activity-plan.navigation.title');
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
