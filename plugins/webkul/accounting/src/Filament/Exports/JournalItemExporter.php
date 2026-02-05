<?php

namespace Webkul\Accounting\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Webkul\Accounting\Models\JournalItem;

class JournalItemExporter extends Exporter
{
    protected static ?string $model = JournalItem::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('move_name')
                ->label(__('accounting::filament/exports/journal-item.columns.number')),
            ExportColumn::make('date')
                ->label(__('accounting::filament/exports/journal-item.columns.date')),
            ExportColumn::make('account.name')
                ->label(__('accounting::filament/exports/journal-item.columns.account')),
            ExportColumn::make('partner.name')
                ->label(__('accounting::filament/exports/journal-item.columns.partner')),
            ExportColumn::make('name')
                ->label(__('accounting::filament/exports/journal-item.columns.label')),
            ExportColumn::make('reference')
                ->label(__('accounting::filament/exports/journal-item.columns.reference')),
            ExportColumn::make('journal.name')
                ->label(__('accounting::filament/exports/journal-item.columns.journal')),
            ExportColumn::make('debit')
                ->label(__('accounting::filament/exports/journal-item.columns.debit')),
            ExportColumn::make('credit')
                ->label(__('accounting::filament/exports/journal-item.columns.credit')),
            ExportColumn::make('balance')
                ->label(__('accounting::filament/exports/journal-item.columns.balance')),
            ExportColumn::make('currency.name')
                ->label(__('accounting::filament/exports/journal-item.columns.currency'))
                ->enabledByDefault(false),
            ExportColumn::make('company.name')
                ->label(__('accounting::filament/exports/journal-item.columns.company'))
                ->enabledByDefault(false),
            ExportColumn::make('parent_state')
                ->label(__('accounting::filament/exports/journal-item.columns.status'))
                ->enabledByDefault(false),
            ExportColumn::make('amount_currency')
                ->label(__('accounting::filament/exports/journal-item.columns.amount-currency'))
                ->enabledByDefault(false),
            ExportColumn::make('amount_residual')
                ->label(__('accounting::filament/exports/journal-item.columns.amount-residual'))
                ->enabledByDefault(false),
            ExportColumn::make('reconciled')
                ->label(__('accounting::filament/exports/journal-item.columns.reconciled'))
                ->formatStateUsing(fn ($state) => $state ? __('accounting::filament/exports/journal-item.values.yes') : __('accounting::filament/exports/journal-item.values.no'))
                ->enabledByDefault(false),
            ExportColumn::make('date_maturity')
                ->label(__('accounting::filament/exports/journal-item.columns.due-date'))
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('accounting::filament/exports/journal-item.notification.completed', [
            'count' => number_format($export->successful_rows),
        ]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.__('accounting::filament/exports/journal-item.notification.failed', [
                'count' => number_format($failedRowsCount),
            ]);
        }

        return $body;
    }
}
