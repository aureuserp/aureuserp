<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\LostReason;

class MarkAsLostAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'markAsLost';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/clusters/sales/resources/opportunity.actions.lost.label'))
            ->icon('heroicon-o-hand-thumb-down')
            ->color('danger')
            ->modalHeading(__('crm::filament/clusters/sales/resources/opportunity.actions.lost.modal.heading'))
            ->modalSubmitActionLabel(__('crm::filament/clusters/sales/resources/opportunity.actions.lost.modal.submit'))
            ->visible(fn (Lead $record): bool => ! $record->trashed() && LeadResourceResolver::resourceFor($record)::canDelete($record))
            ->schema([
                Select::make('lost_reason_id')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.actions.lost.form.lost-reason'))
                    ->options(fn (): array => LostReason::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable(),
                Textarea::make('lost_feedback')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.actions.lost.form.feedback'))
                    ->rows(3),
            ])
            ->action(function (Lead $record, array $data): void {
                $record->markAsLost($data['lost_reason_id'] ?? null, $data['lost_feedback'] ?? null);

                Notification::make()
                    ->success()
                    ->title($record->isLead()
                        ? __('crm::filament/clusters/leads/resources/lead.actions.lost.notification.title')
                        : __('crm::filament/clusters/sales/resources/opportunity.actions.lost.notification.title'))
                    ->body($record->isLead()
                        ? __('crm::filament/clusters/leads/resources/lead.actions.lost.notification.body')
                        : __('crm::filament/clusters/sales/resources/opportunity.actions.lost.notification.body'))
                    ->send();
            });
    }
}
