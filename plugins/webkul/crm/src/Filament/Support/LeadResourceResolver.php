<?php

namespace Webkul\Crm\Filament\Support;

use Illuminate\Database\Eloquent\Model;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Models\Activity;
use Webkul\Crm\Models\Lead;

class LeadResourceResolver
{
    public static function resourceFor(Lead $record): string
    {
        return $record->isLead() ? LeadResource::class : OpportunityResource::class;
    }

    public static function viewUrl(?Lead $record): ?string
    {
        return $record ? static::resourceFor($record)::getUrl('view', ['record' => $record]) : null;
    }

    public static function leadOf(?Model $record): ?Lead
    {
        return match (true) {
            $record instanceof Lead     => $record,
            $record instanceof Activity => $record->lead,
            default                     => null,
        };
    }

    public static function canEdit(?Lead $record): bool
    {
        return $record && ! $record->trashed() && static::resourceFor($record)::canEdit($record);
    }
}
