<?php

namespace Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Actions\ConvertToOpportunityAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsLostAction;
use Webkul\Crm\Filament\Concerns\HasCrmClusterNavigation;
use Webkul\Crm\Filament\Concerns\HasFloatingRecordActionBar;
use Webkul\Crm\Services\LeadScoring;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditLead extends EditRecord
{
    use HasCrmClusterNavigation, HasFloatingRecordActionBar, HasRecordNavigationTabs {
        HasCrmClusterNavigation::getSubNavigation insteadof HasRecordNavigationTabs;
        HasCrmClusterNavigation::getSubNavigationPosition insteadof HasRecordNavigationTabs;
        HasFloatingRecordActionBar::getHeaderWidgets insteadof HasRecordNavigationTabs;
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
            ChatterAction::make()
                ->resource(static::$resource)
                ->activityPlans($this->getRecord()->activityPlans()),
            ViewAction::make(),
        ];
    }

    public function getFloatingRecordActions(): array
    {
        return [
            $this->convertToOpportunityAction,
            $this->markAsLostAction,
        ];
    }

    public function convertToOpportunityAction(): Action
    {
        return ConvertToOpportunityAction::make()
            ->record(fn () => $this->getRecord());
    }

    public function markAsLostAction(): Action
    {
        return MarkAsLostAction::make()
            ->record(fn () => $this->getRecord())
            ->after(fn () => $this->redirect(LeadResource::getUrl('view', ['record' => $this->getRecord()])));
    }

    protected function afterSave(): void
    {
        app(LeadScoring::class)->refresh($this->getRecord()->load('tags'));
    }
}
