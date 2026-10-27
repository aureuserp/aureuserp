<?php

namespace Webkul\Crm\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Webkul\Crm\Models\Tag;

class TagExporter extends CRMRecordExporter
{
    protected static ?string $model = Tag::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label(__('crm::filament/clusters/configurations/resources/tag.table.columns.name')),
            ExportColumn::make('color')->label(__('crm::filament/clusters/configurations/resources/tag.table.columns.color')),
            ExportColumn::make('creator.name')->label(__('crm::filament/clusters/configurations/resources/tag.table.columns.created-by')),
            ExportColumn::make('created_at')->label(__('crm::filament/clusters/configurations/resources/tag.table.columns.created-at')),
        ];
    }

    protected static function getFileNamePrefix(): string
    {
        return 'tags';
    }
}
