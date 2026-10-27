<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\Lead;

class LeadReportBulkActions
{
    public static function make(string $exporter): BulkActionGroup
    {
        return BulkActionGroup::make([
            BulkActionGroup::make([
                RecordBulkActions::export($exporter),
                ArchiveLeadsBulkAction::make(),
                RestoreBulkAction::make()
                    ->label(__('crm::filament/lead-actions.unarchive.label'))
                    ->authorize(fn (): bool => true)
                    ->authorizeIndividualRecords(fn (Lead $record): bool => LeadResourceResolver::resourceFor($record)::canRestore($record))
                    ->hidden(false),
                DuplicateLeadsBulkAction::make(),
                ForceDeleteBulkAction::make()
                    ->label(__('crm::filament/lead-actions.delete.label'))
                    ->icon('heroicon-o-trash')
                    ->authorize(fn (): bool => true)
                    ->authorizeIndividualRecords(fn (Lead $record): bool => LeadResourceResolver::resourceFor($record)::canForceDelete($record))
                    ->hidden(false),
            ])->dropdown(false),
            BulkActionGroup::make([
                MarkLeadsAsLostBulkAction::make(),
                MergeLeadsBulkAction::make(),
                SendLeadsEmailBulkAction::make(),
                SendLeadsSMSBulkAction::make(),
            ])->dropdown(false),
        ]);
    }
}
