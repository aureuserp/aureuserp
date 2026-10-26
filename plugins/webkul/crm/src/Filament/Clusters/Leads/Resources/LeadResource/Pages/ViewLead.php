<?php

namespace Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Actions\ConvertToOpportunityAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsLostAction;
use Webkul\Crm\Filament\Concerns\HasCrmClusterNavigation;
use Webkul\Crm\Models\Lead;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewLead extends ViewRecord
{
    use HasCrmClusterNavigation, HasRecordNavigationTabs {
        HasCrmClusterNavigation::getSubNavigation insteadof HasRecordNavigationTabs;
        HasCrmClusterNavigation::getSubNavigationPosition insteadof HasRecordNavigationTabs;
    }

    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ConvertToOpportunityAction::make(),
            MarkAsLostAction::make()
                ->after(fn () => $this->refreshRecord()),
            ChatterAction::make()
                ->resource(static::$resource)
                ->activityPlans($this->getRecord()->activityPlans()),
            RestoreAction::make()
                ->after(fn () => $this->refreshRecord()),
            EditAction::make()
                ->hidden(fn (Lead $record): bool => $record->trashed()),
            ForceDeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('crm::filament/clusters/leads/resources/lead.actions.force-delete.notification.title'))
                        ->body(__('crm::filament/clusters/leads/resources/lead.actions.force-delete.notification.body')),
                ),
        ];
    }

    protected function refreshRecord(): void
    {
        $this->record = LeadResource::getEloquentQuery()->findOrFail($this->getRecord()->getKey());

        $this->fillForm();
    }
}
