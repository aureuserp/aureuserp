<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsLostAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsWonAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\NewQuotationAction;
use Webkul\Crm\Filament\Concerns\HasFloatingRecordActionBar;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditOpportunity extends EditRecord
{
    use HasFloatingRecordActionBar, HasRecordNavigationTabs {
        HasFloatingRecordActionBar::getHeaderWidgets insteadof HasRecordNavigationTabs;
    }

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
            NewQuotationAction::make(),
            ChatterAction::make()
                ->resource(static::$resource)
                ->activityPlans($this->getRecord()->activityPlans()),
            ViewAction::make(),
        ];
    }

    protected function refreshRecord(): void
    {
        $this->record = OpportunityResource::getEloquentQuery()->findOrFail($this->getRecord()->getKey());

        $this->fillForm();
    }

    public function getFloatingRecordActions(): array
    {
        return [
            $this->markAsWonAction,
            $this->markAsLostAction,
        ];
    }

    public function markAsWonAction(): Action
    {
        return MarkAsWonAction::make()
            ->record(fn () => $this->getRecord())
            ->after(fn () => $this->refreshRecord());
    }

    public function markAsLostAction(): Action
    {
        return MarkAsLostAction::make()
            ->record(fn () => $this->getRecord())
            ->after(fn () => $this->redirect(OpportunityResource::getUrl('view', ['record' => $this->getRecord()])));
    }
}
