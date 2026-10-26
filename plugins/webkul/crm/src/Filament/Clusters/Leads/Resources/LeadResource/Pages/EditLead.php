<?php

namespace Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Actions\ConvertToOpportunityAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsLostAction;
use Webkul\Crm\Filament\Concerns\HasCrmClusterNavigation;
use Webkul\Crm\Services\LeadScoring;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditLead extends EditRecord
{
    use HasCrmClusterNavigation, HasRecordNavigationTabs {
        HasCrmClusterNavigation::getSubNavigation insteadof HasRecordNavigationTabs;
        HasCrmClusterNavigation::getSubNavigationPosition insteadof HasRecordNavigationTabs;
    }

    protected static string $resource = LeadResource::class;

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('crm::filament/clusters/leads/resources/lead/pages/edit-lead.notification.title'))
            ->body(__('crm::filament/clusters/leads/resources/lead/pages/edit-lead.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ConvertToOpportunityAction::make(),
            MarkAsLostAction::make()
                ->after(fn () => $this->redirect(LeadResource::getUrl('view', ['record' => $this->getRecord()]))),
            ChatterAction::make()
                ->resource(static::$resource)
                ->activityPlans($this->getRecord()->activityPlans()),
            ViewAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        app(LeadScoring::class)->refresh($this->getRecord()->load('tags'));
    }
}
