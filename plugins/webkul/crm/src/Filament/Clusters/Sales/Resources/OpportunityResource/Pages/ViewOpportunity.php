<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsLostAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsWonAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\NewQuotationAction;
use Webkul\Crm\Filament\Concerns\HasFloatingRecordActionBar;
use Webkul\Crm\Models\Lead;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewOpportunity extends ViewRecord
{
    use HasFloatingRecordActionBar, HasRecordNavigationTabs {
        HasFloatingRecordActionBar::getHeaderWidgets insteadof HasRecordNavigationTabs;
    }

    protected static string $resource = OpportunityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            NewQuotationAction::make(),
            ChatterAction::make()
                ->resource(static::$resource)
                ->activityPlans($this->getRecord()->activityPlans()),
            RestoreAction::make()
                ->label(__('crm::filament/clusters/sales/resources/opportunity/pages/view-opportunity.header-actions.restore.label'))
                ->after(fn () => $this->refreshRecord()),
            EditAction::make()->hidden(fn (Lead $record): bool => $record->trashed()),
            ForceDeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('crm::filament/clusters/sales/resources/opportunity/pages/view-opportunity.header-actions.force-delete.notification.title'))
                        ->body(__('crm::filament/clusters/sales/resources/opportunity/pages/view-opportunity.header-actions.force-delete.notification.body')),
                ),
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
            ->after(fn () => $this->refreshRecord());
    }
}
