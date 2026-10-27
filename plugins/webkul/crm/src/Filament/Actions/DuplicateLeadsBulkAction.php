<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\Lead;

class DuplicateLeadsBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'duplicateLeads';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/lead-actions.duplicate.label'))
            ->icon('heroicon-o-document-duplicate')
            ->visible(fn (): bool => LeadResource::canCreate() || OpportunityResource::canCreate())
            ->deselectRecordsAfterCompletion()
            ->action(function (Collection $records): void {
                $leads = $records->filter(fn (Lead $lead): bool => static::canDuplicate($lead));

                $leads->each(fn (Lead $lead): Lead => static::duplicate($lead));

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/lead-actions.duplicate.notification', ['count' => $leads->count()]))
                    ->send();
            });
    }

    public static function canDuplicate(?Lead $lead): bool
    {
        if (! $lead) {
            return false;
        }

        $resource = LeadResourceResolver::resourceFor($lead);

        return $resource::canCreate() && $resource::canView($lead);
    }

    public static function duplicate(Lead $lead): Lead
    {
        $copy = $lead->replicate(['deleted_at', 'closed_at', 'converted_at', 'assigned_at', 'lost_reason_id', 'lost_feedback']);
        $copy->name = __('crm::filament/lead-actions.duplicate.copy', ['name' => $lead->name]);
        $copy->creator_id = Auth::id();
        $copy->save();

        $copy->tags()->sync($lead->tags()->allRelatedIds());

        return $copy;
    }
}
