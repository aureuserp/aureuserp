<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Crm\Filament\Actions\SendLeadEmailAction;
use Webkul\Crm\Filament\Actions\SendLeadSMSAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsLostAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsWonAction;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewOpportunity extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = OpportunityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource(static::$resource)
                ->activityPlans($this->getRecord()->activityPlans()),
            MarkAsWonAction::make()
                ->after(fn () => $this->refreshRecord()),
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
        $this->record = OpportunityResource::getEloquentQuery()->findOrFail($this->getRecord()->getKey());

        $this->fillForm();
    }
}
