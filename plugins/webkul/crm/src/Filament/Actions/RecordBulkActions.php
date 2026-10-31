<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;

class RecordBulkActions
{
    public static function make(?string $exporter = null, bool $softDeletes = false, array $actions = []): BulkActionGroup
    {
        return BulkActionGroup::make([
            BulkActionGroup::make(array_values(array_filter([
                $exporter ? static::export($exporter) : null,
                $softDeletes ? RestoreBulkAction::make()->hidden(false) : null,
                DeleteBulkAction::make(),
                $softDeletes ? ForceDeleteBulkAction::make()->hidden(false) : null,
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
}
