<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\LostReason;

class MarkLeadsAsLostBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'markLeadsAsLost';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/lead-actions.mark-lost.label'))
            ->icon('heroicon-o-hand-thumb-down')
            ->modalHeading(__('crm::filament/lead-actions.mark-lost.label'))
            ->modalSubmitActionLabel(__('crm::filament/clusters/sales/resources/opportunity.actions.lost.modal.submit'))
            ->deselectRecordsAfterCompletion()
            ->schema([
                Select::make('lost_reason_id')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.actions.lost.form.lost-reason'))
                    ->options(fn (): array => LostReason::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable(),
                Textarea::make('lost_feedback')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.actions.lost.form.feedback'))
                    ->rows(3),
            ])
            ->action(function (Collection $records, array $data): void {
                $leads = $records->filter(fn (Lead $lead): bool => ! $lead->trashed() && LeadResourceResolver::resourceFor($lead)::canDelete($lead));

                $leads->each(fn (Lead $lead) => $lead->markAsLost($data['lost_reason_id'] ?? null, $data['lost_feedback'] ?? null));

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/lead-actions.mark-lost.notification', ['count' => $leads->count()]))
                    ->send();
            });
    }
}
