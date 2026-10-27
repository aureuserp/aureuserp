<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Pages\EditActivityPlan;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Pages\ListActivityPlans;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Pages\ManageActivities;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Pages\ViewActivityPlan;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Tables\ActivityPlansTable;
use Webkul\Crm\Models\ActivityPlan;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource as BaseActivityPlanResource;

class ActivityPlanResource extends BaseActivityPlanResource
{
    protected static ?string $model = ActivityPlan::class;

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 6;

    public static function getModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/activity-plan.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/activity-plan.plural-model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/activity-plan.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('crm::filament/clusters/configurations/resources/activity-plan.navigation.group');
    }

    public static function table(Table $table): Table
    {
        return ActivityPlansTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('plugin', 'crm');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewActivityPlan::class,
            EditActivityPlan::class,
            ManageActivities::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListActivityPlans::route('/'),
            'view'       => ViewActivityPlan::route('/{record}'),
            'edit'       => EditActivityPlan::route('/{record}/edit'),
            'activities' => ManageActivities::route('/{record}/activities'),
        ];
    }
}
