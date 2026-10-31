<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\Lead;

class MarkAsWonAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'markAsWon';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/clusters/sales/resources/opportunity.actions.won.label'))
            ->icon('heroicon-o-trophy')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading(__('crm::filament/clusters/sales/resources/opportunity.actions.won.modal.heading'))
            ->modalDescription(__('crm::filament/clusters/sales/resources/opportunity.actions.won.modal.description'))
            ->visible(fn (Lead $record): bool => ! $record->isLead() && ! $record->trashed() && ! $record->isWon() && LeadResourceResolver::resourceFor($record)::canEdit($record))
            ->action(function (Lead $record): void {
                if (! $record->markAsWon()) {
                    Notification::make()
                        ->warning()
                        ->title(__('crm::filament/clusters/sales/resources/opportunity.actions.won.notification.no-won-stage.title'))
                        ->body(__('crm::filament/clusters/sales/resources/opportunity.actions.won.notification.no-won-stage.body'))
                        ->send();

                    return;
                }

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/clusters/sales/resources/opportunity.actions.won.notification.success.title'))
                    ->body(__('crm::filament/clusters/sales/resources/opportunity.actions.won.notification.success.body'))
                    ->send();
            });
    }
}
