<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityPlans;

use Filament\Tables\Table;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\ActivityPlans\ActivityPlanResource as BaseActivityPlanResource;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityPlans\Pages\EditActivityPlan;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityPlans\Pages\ListActivityPlans;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityPlans\Pages\ViewActivityPlan;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityPlans\Tables\ActivityPlansTable;
use Webkul\Recruitment\Models\ActivityPlan;

class ActivityPlanResource extends BaseActivityPlanResource
{
    protected static ?string $model = ActivityPlan::class;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/activity-plan.navigation.group');
    }

    public static function table(Table $table): Table
    {
        return ActivityPlansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListActivityPlans::route('/'),
            'edit'   => EditActivityPlan::route('/{record}/edit'),
            'view'   => ViewActivityPlan::route('/{record}'),
        ];
    }
}
