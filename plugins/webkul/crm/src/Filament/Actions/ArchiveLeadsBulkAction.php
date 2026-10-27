<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\Lead;

class ArchiveLeadsBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'archiveLeads';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/lead-actions.archive.label'))
            ->icon('heroicon-o-archive-box')
            ->requiresConfirmation()
            ->modalDescription(__('crm::filament/lead-actions.archive.bulk-description'))
            ->deselectRecordsAfterCompletion()
            ->action(function (Collection $records): void {
                $leads = $records->filter(fn (Lead $lead): bool => static::canArchive($lead));

                $leads->each(fn (Lead $lead): ?bool => $lead->delete());

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/lead-actions.archive.notification', ['count' => $leads->count()]))
                    ->send();
            });
    }

    public static function canArchive(?Lead $lead): bool
    {
        return $lead && ! $lead->trashed() && LeadResourceResolver::resourceFor($lead)::canDelete($lead);
    }
}
