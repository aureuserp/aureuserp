<?php

namespace Webkul\Crm\Filament\Support\Reports;

use Illuminate\Support\Facades\DB;

class Sql
{
    public static function month(string $expression): string
    {
        return match (DB::connection()->getDriverName()) {
            'pgsql'  => "to_char({$expression}, 'YYYY-MM')",
            'sqlite' => "strftime('%Y-%m', {$expression})",
            'sqlsrv' => "format({$expression}, 'yyyy-MM')",
            default  => "date_format({$expression}, '%Y-%m')",
        };
    }

    public static function daysBetween(string $from, string $to): string
    {
        return match (DB::connection()->getDriverName()) {
            'pgsql'  => "(extract(epoch from ({$to} - {$from})) / 86400)",
            'sqlite' => "(julianday({$to}) - julianday({$from}))",
            'sqlsrv' => "(datediff(second, {$from}, {$to}) / 86400.0)",
            default  => "(timestampdiff(second, {$from}, {$to}) / 86400)",
        };
    }
}
