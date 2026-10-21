<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\ExportAction;
use Illuminate\Database\Eloquent\Builder;

class CrmExportAction extends ExportAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/transfers.actions.export.label'))
            ->icon('heroicon-o-arrow-up-tray')
            ->color('gray')
            ->modifyQueryUsing(fn (Builder $query): Builder => static::limitToVisibleRecords($query));
    }

    public static function limitToVisibleRecords(Builder $query): Builder
    {
        $model = $query->getModel();

        $visibleIds = (clone $query)
            ->reorder()
            ->pluck($model->getQualifiedKeyName())
            ->all();

        return $query->whereIn($model->getQualifiedKeyName(), $visibleIds);
    }
}
