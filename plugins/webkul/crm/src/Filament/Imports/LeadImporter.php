<?php

namespace Webkul\Crm\Filament\Imports;

use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Policies\LeadRecordPolicy;

class LeadImporter extends LeadRecordImporter
{
    protected static function getLeadType(): LeadType
    {
        return LeadType::LEAD;
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
