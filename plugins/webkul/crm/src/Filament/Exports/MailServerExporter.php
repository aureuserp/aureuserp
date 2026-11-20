<?php

namespace Webkul\Crm\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Webkul\Crm\Models\MailServer;

class MailServerExporter extends CRMRecordExporter
{
    protected static ?string $model = MailServer::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label(__('crm::filament/clusters/configurations/resources/mail-server.table.columns.name')),
            ExportColumn::make('host')->label(__('crm::filament/clusters/configurations/resources/mail-server.table.columns.host')),
            ExportColumn::make('folder')->label(__('crm::filament/clusters/configurations/resources/mail-server.table.columns.folder')),
            ExportColumn::make('is_active')->label(__('crm::filament/clusters/configurations/resources/mail-server.table.columns.active')),
            ExportColumn::make('last_fetched_at')->label(__('crm::filament/clusters/configurations/resources/mail-server.table.columns.last-fetched')),
            ExportColumn::make('last_error')->label(__('crm::filament/clusters/configurations/resources/mail-server.table.columns.last-error')),
        ];
    }

    protected static function getFileNamePrefix(): string
    {
        return 'mail-servers';
    }
}
