<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlans\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Webkul\TimeOff\Traits\LeaveAccrualPlan;

class MilestoneRelationManager extends RelationManager
{
    use LeaveAccrualPlan;

    protected static string $relationship = 'leaveAccrualLevels';
}
