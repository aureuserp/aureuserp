<?php

namespace Webkul\Crm\Filament\Imports;

use Webkul\Crm\Models\Lead;
use Webkul\Crm\Policies\LeadRecordPolicy;

class LeadImporter extends LeadRecordImporter
{
    protected static function getLeadType(): string
    {
        return Lead::TYPE_LEAD;
    }

    protected static function getPolicy(): string
    {
        return LeadRecordPolicy::class;
    }

    public static function getColumns(): array
    {
        return static::leadColumns();
    }
}
