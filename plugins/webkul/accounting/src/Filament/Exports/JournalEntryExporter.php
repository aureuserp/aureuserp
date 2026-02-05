<?php

namespace Webkul\Accounting\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Webkul\Accounting\Models\JournalEntry;

class JournalEntryExporter extends Exporter
{
    protected static ?string $model = JournalEntry::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('invoice_date')
                ->label(__('accounting::filament/exports/journal-entry.columns.invoice-date'))
                ->enabledByDefault(false),
            ExportColumn::make('date')
                ->label(__('accounting::filament/exports/journal-entry.columns.date')),
            ExportColumn::make('name')
                ->label(__('accounting::filament/exports/journal-entry.columns.number')),
            ExportColumn::make('invoice_partner_display_name')
                ->label(__('accounting::filament/exports/journal-entry.columns.partner')),
            ExportColumn::make('reference')
                ->label(__('accounting::filament/exports/journal-entry.columns.reference')),
            ExportColumn::make('journal.name')
                ->label(__('accounting::filament/exports/journal-entry.columns.journal')),
            ExportColumn::make('company.name')
                ->label(__('accounting::filament/exports/journal-entry.columns.company')),
            ExportColumn::make('amount_total_in_currency_signed')
                ->label(__('accounting::filament/exports/journal-entry.columns.total')),
            ExportColumn::make('state')
                ->label(__('accounting::filament/exports/journal-entry.columns.state'))
                ->formatStateUsing(fn ($state) => is_object($state) ? ($state->getLabel() ?? $state->value ?? '') : (string) $state),
            ExportColumn::make('checked')
                ->label(__('accounting::filament/exports/journal-entry.columns.checked'))
                ->formatStateUsing(fn ($state) => $state ? __('accounting::filament/exports/journal-entry.values.yes') : __('accounting::filament/exports/journal-entry.values.no'))
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('accounting::filament/exports/journal-entry.notification.completed', [
            'count' => number_format($export->successful_rows),
        ]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.__('accounting::filament/exports/journal-entry.notification.failed', [
                'count' => number_format($failedRowsCount),
            ]);
        }

        return $body;
    }
}
