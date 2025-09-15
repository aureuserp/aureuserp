<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\Pages;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\ManageRelatedRecords;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource;
use Webkul\TimeOff\Traits\LeaveAccrualPlan;

class ManageMilestone extends ManageRelatedRecords
{
    use LeaveAccrualPlan;

    protected static string $resource = AccrualPlanResource::class;

    protected static string $relationship = 'leaveAccrualLevels';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/clusters/configurations/resources/accrual-plan/pages/manage-milestone.navigation.label');
    }
}
