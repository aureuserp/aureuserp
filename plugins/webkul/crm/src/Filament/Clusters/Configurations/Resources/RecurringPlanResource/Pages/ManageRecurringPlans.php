<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\RecurringPlanResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RecurringPlanResource;
use Webkul\Crm\Models\RecurringPlan;

class ManageRecurringPlans extends ManageRecords
{
    protected static string $resource = RecurringPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('crm::filament/clusters/configurations/resources/recurring-plan.header-actions.create'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(Notification::make()->success()->title(__('crm::filament/clusters/configurations/resources/recurring-plan.notifications.created'))),
        ];
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make(__('crm::filament/clusters/configurations/resources/recurring-plan.tabs.active'))
                ->badge(fn (): int => RecurringPlan::query()->count())
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutTrashed()),
            'archived' => Tab::make(__('crm::filament/clusters/configurations/resources/recurring-plan.tabs.archived'))
                ->badge(fn (): int => RecurringPlan::onlyTrashed()->count())
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->onlyTrashed()),
        ];
    }
}
