<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsLostAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsWonAction;
use Webkul\Crm\Services\LeadScoring;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditOpportunity extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = OpportunityResource::class;

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('crm::filament/clusters/sales/resources/opportunity/pages/edit-opportunity.notification.title'))
            ->body(__('crm::filament/clusters/sales/resources/opportunity/pages/edit-opportunity.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource(static::$resource)
                ->activityPlans($this->getRecord()->activityPlans()),
            MarkAsWonAction::make()
                ->after(fn () => $this->refreshRecord()),
            MarkAsLostAction::make()
                ->after(fn () => $this->redirect(OpportunityResource::getUrl('view', ['record' => $this->getRecord()]))),
        ];
    }

    protected function refreshRecord(): void
    {
        $this->record = OpportunityResource::getEloquentQuery()->findOrFail($this->getRecord()->getKey());

        $this->fillForm();
    }

    protected function afterSave(): void
    {
        app(LeadScoring::class)->refresh($this->getRecord()->load('tags'));
    }
}
