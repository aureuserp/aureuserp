<?php

namespace Webkul\Crm\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Webkul\Crm\Models\InboundEmail;

class InboundEmailExporter extends CRMRecordExporter
{
    protected static ?string $model = InboundEmail::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('received_at')->label(__('crm::filament/clusters/configurations/resources/inbound-email.table.columns.received-at')),
            ExportColumn::make('from_email')->label(__('crm::filament/clusters/configurations/resources/inbound-email.table.columns.from')),
            ExportColumn::make('subject')->label(__('crm::filament/clusters/configurations/resources/inbound-email.table.columns.subject')),
            ExportColumn::make('status')->label(__('crm::filament/clusters/configurations/resources/inbound-email.table.columns.status'))->formatStateUsing(fn (mixed $state): string => static::enumLabel($state)),
            ExportColumn::make('alias.team.name')->label(__('crm::filament/clusters/configurations/resources/inbound-email.table.columns.team')),
            ExportColumn::make('lead.name')->label(__('crm::filament/clusters/configurations/resources/inbound-email.table.columns.lead')),
            ExportColumn::make('reason')->label(__('crm::filament/clusters/configurations/resources/inbound-email.table.columns.reason')),
            ExportColumn::make('mailServer.name')->label(__('crm::filament/clusters/configurations/resources/inbound-email.table.columns.server')),
        ];
    }

    protected static function getFileNamePrefix(): string
    {
        return 'received-emails';
    }
}
