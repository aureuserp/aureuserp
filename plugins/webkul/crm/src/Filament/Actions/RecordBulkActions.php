<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Illuminate\Database\Eloquent\Collection;

class RecordBulkActions
{
    public static function make(?string $exporter = null, bool $archivable = false, bool $duplicable = true, array $copyRelations = [], array $actions = []): BulkActionGroup
    {
        return BulkActionGroup::make([
            BulkActionGroup::make(array_values(array_filter([
                $exporter ? static::export($exporter) : null,
                $archivable ? static::archive() : null,
                $archivable ? static::unarchive() : null,
                $duplicable ? DuplicateRecordsBulkAction::make()->copyRelations($copyRelations) : null,
                static::delete($archivable),
            ])))->dropdown(false),
            ...($actions ? [BulkActionGroup::make($actions)->dropdown(false)] : []),
        ]);
    }

    public static function export(string $exporter): ExportBulkAction
    {
        return ExportBulkAction::make()
            ->label(__('crm::filament/transfers.actions.export.label'))
            ->icon('heroicon-o-arrow-up-tray')
            ->exporter($exporter);
    }

    public static function archive(): DeleteBulkAction
    {
        return DeleteBulkAction::make('archiveRecords')
            ->label(__('crm::filament/lead-actions.archive.label'))
            ->icon('heroicon-o-archive-box')
            ->modalHeading(__('crm::filament/lead-actions.archive.label'))
            ->modalDescription(__('crm::filament/lead-actions.archive.bulk-description'))
            ->modalSubmitActionLabel(__('crm::filament/lead-actions.archive.label'))
            ->successNotificationTitle(fn (Collection $records): string => __('crm::filament/lead-actions.archive.notification', ['count' => $records->count()]));
    }

    public static function unarchive(): RestoreBulkAction
    {
        return RestoreBulkAction::make()
            ->label(__('crm::filament/lead-actions.unarchive.label'))
            ->hidden(false);
    }

    public static function delete(bool $archivable): DeleteBulkAction|ForceDeleteBulkAction
    {
        $action = $archivable ? ForceDeleteBulkAction::make()->hidden(false) : DeleteBulkAction::make();

        return $action
            ->label(__('crm::filament/lead-actions.delete.label'))
            ->icon('heroicon-o-trash');
    }
}
