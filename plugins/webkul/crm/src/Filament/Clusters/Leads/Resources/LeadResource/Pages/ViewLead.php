<?php

namespace Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Crm\Filament\Actions\SendLeadEmailAction;
use Webkul\Crm\Filament\Actions\SendLeadSMSAction;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Actions\ConvertToOpportunityAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsLostAction;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewLead extends ViewRecord
{
    use HasCRMClusterNavigation, HasRecordNavigationTabs {
        HasCRMClusterNavigation::getSubNavigation insteadof HasRecordNavigationTabs;
        HasCRMClusterNavigation::getSubNavigationPosition insteadof HasRecordNavigationTabs;
    }

    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource(static::$resource)
                ->activityPlans($this->getRecord()->activityPlans()),
            ConvertToOpportunityAction::make(),
            MarkAsLostAction::make()
                ->after(fn () => $this->refreshRecord()),
            SendLeadEmailAction::make(),
            SendLeadSMSAction::make(),
            DeleteAction::make(),
            RestoreAction::make()
                ->after(fn () => $this->refreshRecord()),
        ];
    }

    protected function refreshRecord(): void
    {
        $this->record = LeadResource::getEloquentQuery()->findOrFail($this->getRecord()->getKey());

        $this->fillForm();
    }
}
