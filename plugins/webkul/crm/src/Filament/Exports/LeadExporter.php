<?php

namespace Webkul\Crm\Filament\Exports;

use Filament\Actions\Exports\Models\Export;

class LeadExporter extends LeadRecordExporter
{
    public static function getColumns(): array
    {
        return static::leadColumns();
    }

    public function getFileName(Export $export): string
    {
        return 'leads-'.$export->getKey().'-'.now()->format('Y-m-d');
    }
}
