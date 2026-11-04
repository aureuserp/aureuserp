<?php

namespace Webkul\Crm\Filament\Exports;

use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Support\Contracts\HasLabel;
use Webkul\Crm\Filament\Concerns\UsesAbsoluteDownloadUrls;

abstract class CRMRecordExporter extends Exporter
{
    use UsesAbsoluteDownloadUrls;

    abstract protected static function getFileNamePrefix(): string;

    protected static function enumLabel(mixed $state): string
    {
        return $state instanceof HasLabel ? (string) $state->getLabel() : (string) ($state->value ?? $state);
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('crm::filament/transfers.notifications.export.completed', ['count' => number_format($export->successful_rows)]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.__('crm::filament/transfers.notifications.export.failed', ['count' => number_format($failedRowsCount)]);
        }

        return $body;
    }

    public function getFileName(Export $export): string
    {
        return static::getFileNamePrefix().'-'.$export->getKey().'-'.now()->format('Y-m-d');
    }
}
