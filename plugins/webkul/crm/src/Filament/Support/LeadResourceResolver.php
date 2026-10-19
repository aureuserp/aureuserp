<?php

namespace Webkul\Crm\Filament\Support;

use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Models\Lead;

/**
 * Leads and opportunities share the Lead model but live in different resources;
 * this picks the resource that owns a given record.
 */
class LeadResourceResolver
{
    /**
     * @return class-string<LeadResource|OpportunityResource>
     */
    public static function resourceFor(Lead $record): string
    {
        return $record->isLead() ? LeadResource::class : OpportunityResource::class;
    }

    public static function viewUrl(?Lead $record): ?string
    {
        return $record ? static::resourceFor($record)::getUrl('view', ['record' => $record]) : null;
    }
}
