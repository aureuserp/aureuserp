<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource;

class ListAccrualPlans extends ListRecords
{
    protected static string $resource = AccrualPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan/pages/list-accrual-plan.header-actions.new-accrual-plan'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
